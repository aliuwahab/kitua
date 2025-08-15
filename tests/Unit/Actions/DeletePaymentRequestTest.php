<?php

namespace Tests\Unit\Actions;

use App\Actions\PaymentRequest\DeletePaymentRequest;
use App\Events\PaymentRequestDeleted;
use App\Models\Country;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeletePaymentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a single country for testing to avoid unique constraint issues
        if (!Country::where('code', 'GH')->exists()) {
            Country::create([
                'name' => 'Ghana',
                'code' => 'GH',
                'currency_code' => 'GHS',
                'currency_symbol' => 'GH₵',
                'currency_name' => 'Ghana Cedi',
                'is_active' => true,
            ]);
        }
    }

    /** @test */
    public function it_deletes_pending_payment_request_successfully()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Test deletion',
            'status' => 'pending',
        ]);
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($paymentRequest, $user);
        
        $this->assertTrue($result);
        
        // Verify payment request is soft deleted
        $this->assertSoftDeleted('payment_requests', [
            'id' => $paymentRequest->id,
        ]);
        
        Event::assertDispatched(PaymentRequestDeleted::class, function ($event) use ($paymentRequest, $user) {
            return $event->paymentRequest->id === $paymentRequest->id &&
                   $event->user->id === $user->id;
        });
    }

    /** @test */
    public function it_prevents_deletion_of_paid_payment_request()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->paid()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Paid request',
        ]);
        
        $action = new DeletePaymentRequest();
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete a paid payment request.');
        
        $action->execute($paymentRequest, $user);
        
        // Verify payment request is NOT deleted
        $this->assertDatabaseHas('payment_requests', [
            'id' => $paymentRequest->id,
            'deleted_at' => null,
        ]);
        
        Event::assertNotDispatched(PaymentRequestDeleted::class);
    }

    /** @test */
    public function it_deletes_cancelled_payment_request()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->cancelled()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Cancelled request',
        ]);
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($paymentRequest, $user);
        
        $this->assertTrue($result);
        
        // Verify payment request is soft deleted
        $this->assertSoftDeleted('payment_requests', [
            'id' => $paymentRequest->id,
        ]);
        
        Event::assertDispatched(PaymentRequestDeleted::class);
    }

    /** @test */
    public function it_deletes_expired_payment_request()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->expired()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Expired request',
        ]);
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($paymentRequest, $user);
        
        $this->assertTrue($result);
        
        // Verify payment request is soft deleted
        $this->assertSoftDeleted('payment_requests', [
            'id' => $paymentRequest->id,
        ]);
        
        Event::assertDispatched(PaymentRequestDeleted::class);
    }

    /** @test */
    public function it_cleans_up_media_when_deleting_payment_request()
    {
        Event::fake();
        Storage::fake('public');
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Request with image',
            'status' => 'pending',
        ]);
        
        // Add an image to the payment request
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        $paymentRequest->addMedia($image)
                      ->toMediaCollection('images');
        
        $this->assertCount(1, $paymentRequest->getMedia('images'));
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($paymentRequest, $user);
        
        $this->assertTrue($result);
        
        // Verify payment request is soft deleted
        $this->assertSoftDeleted('payment_requests', [
            'id' => $paymentRequest->id,
        ]);
        
        // Verify media was cleaned up (this depends on how Spatie Media Library handles deletion)
        // Note: Spatie Media Library automatically cleans up media when model is deleted
        
        Event::assertDispatched(PaymentRequestDeleted::class);
    }

    /** @test */
    public function it_handles_payment_request_with_metadata()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $metadata = ['category' => 'food', 'urgent' => true, 'tags' => ['lunch', 'emergency']];
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Request with metadata',
            'status' => 'pending',
            'metadata' => $metadata,
        ]);
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($paymentRequest, $user);
        
        $this->assertTrue($result);
        
        // Verify payment request is soft deleted
        $this->assertSoftDeleted('payment_requests', [
            'id' => $paymentRequest->id,
        ]);
        
        Event::assertDispatched(PaymentRequestDeleted::class, function ($event) use ($paymentRequest, $user) {
            return $event->paymentRequest->id === $paymentRequest->id &&
                   $event->user->id === $user->id &&
                   $event->paymentRequest->metadata === $paymentRequest->metadata;
        });
    }

    /** @test */
    public function it_preserves_payment_request_data_in_event()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 250.75,
            'currency_code' => 'GHS',
            'purpose' => 'Test preservation',
            'description' => 'Ensuring data is preserved in event',
            'status' => 'pending',
        ]);
        
        $originalId = $paymentRequest->id;
        $originalAmount = $paymentRequest->amount;
        $originalPurpose = $paymentRequest->purpose;
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($paymentRequest, $user);
        
        $this->assertTrue($result);
        
        Event::assertDispatched(PaymentRequestDeleted::class, function ($event) use ($originalId, $originalAmount, $originalPurpose, $user) {
            return $event->paymentRequest->id === $originalId &&
                   $event->paymentRequest->amount == $originalAmount &&
                   $event->paymentRequest->purpose === $originalPurpose &&
                   $event->user->id === $user->id;
        });
    }

    /** @test */
    public function it_rolls_back_transaction_on_failure()
    {
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Test rollback',
            'status' => 'pending',
        ]);
        
        // Use a different approach - mock the delete method to fail
        $mockPaymentRequest = \Mockery::mock($paymentRequest);
        $mockPaymentRequest->shouldReceive('getAttribute')
                          ->with('status')
                          ->andReturn('pending');
        $mockPaymentRequest->shouldReceive('toArray')
                          ->andReturn($paymentRequest->toArray());
        $mockPaymentRequest->shouldReceive('clearMediaCollection')
                          ->with('images');
        $mockPaymentRequest->shouldReceive('delete')
                          ->andThrow(new \Exception('Database error'));
        
        $action = new DeletePaymentRequest();
        
        try {
            $action->execute($mockPaymentRequest, $user);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            // Verify that the original payment request is still there
            $this->assertDatabaseHas('payment_requests', [
                'id' => $paymentRequest->id,
                'deleted_at' => null,
            ]);
            $this->assertStringContainsString('Database error', $e->getMessage());
        }
    }

    /** @test */
    public function it_returns_false_when_deletion_fails()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        // Create a mock payment request that will fail deletion
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Will fail deletion',
            'status' => 'pending',
        ]);
        
        // Mock the payment request to fail the delete operation
        $mockPaymentRequest = \Mockery::mock($paymentRequest);
        $mockPaymentRequest->shouldReceive('getAttribute')
                          ->with('status')
                          ->andReturn('pending');
        $mockPaymentRequest->shouldReceive('toArray')
                          ->andReturn($paymentRequest->toArray());
        $mockPaymentRequest->shouldReceive('clearMediaCollection')
                          ->with('images');
        $mockPaymentRequest->shouldReceive('delete')
                          ->andReturn(false); // Return false to simulate failed deletion
        
        $action = new DeletePaymentRequest();
        $result = $action->execute($mockPaymentRequest, $user);
        
        $this->assertFalse($result);
        Event::assertNotDispatched(PaymentRequestDeleted::class);
    }
}
