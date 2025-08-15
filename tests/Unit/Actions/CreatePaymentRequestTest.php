<?php

namespace Tests\Unit\Actions;

use App\Actions\PaymentRequest\CreatePaymentRequest;
use App\Events\PaymentRequestCreated;
use App\Models\Country;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreatePaymentRequestTest extends TestCase
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
    public function it_creates_a_payment_request_successfully()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        $data = [
            'amount' => 150.50,
            'currency_code' => 'GHS',
            'purpose' => 'Lunch money',
            'description' => 'Need money for lunch today',
            'expires_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
        $this->assertEquals($user->id, $paymentRequest->user_id);
        $this->assertEquals(150.50, $paymentRequest->amount);
        $this->assertEquals('GHS', $paymentRequest->currency_code);
        $this->assertEquals('Lunch money', $paymentRequest->purpose);
        $this->assertEquals('Need money for lunch today', $paymentRequest->description);
        $this->assertEquals('pending', $paymentRequest->status);
        $this->assertNotNull($paymentRequest->id);
        $this->assertNull($paymentRequest->paid_at);
        
        Event::assertDispatched(PaymentRequestCreated::class, function ($event) use ($paymentRequest, $user) {
            return $event->paymentRequest->id === $paymentRequest->id &&
                   $event->user->id === $user->id;
        });
    }

    /** @test */
    public function it_creates_payment_request_without_optional_fields()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        $data = [
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Coffee money',
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
        $this->assertEquals($user->id, $paymentRequest->user_id);
        $this->assertEquals(100.00, $paymentRequest->amount);
        $this->assertEquals('GHS', $paymentRequest->currency_code);
        $this->assertEquals('Coffee money', $paymentRequest->purpose);
        $this->assertNull($paymentRequest->description);
        $this->assertNull($paymentRequest->expires_at);
        $this->assertEquals('pending', $paymentRequest->status);
        
        Event::assertDispatched(PaymentRequestCreated::class);
    }

    /** @test */
    public function it_handles_image_upload_when_provided()
    {
        Event::fake();
        Storage::fake('public');
        
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        
        $data = [
            'amount' => 200.00,
            'currency_code' => 'GHS',
            'purpose' => 'Groceries',
            'description' => 'Grocery shopping receipt',
            'image' => $image,
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
        
        // Check that media was attached
        $this->assertCount(1, $paymentRequest->getMedia('images'));
        
        $media = $paymentRequest->getFirstMedia('images');
        $this->assertEquals('receipt', $media->name);  // Spatie Media Library strips extension from name
        $this->assertEquals('receipt.jpg', $media->file_name);
        $this->assertEquals('image/jpeg', $media->mime_type);
        
        Event::assertDispatched(PaymentRequestCreated::class);
    }

    /** @test */
    public function it_sets_metadata_when_provided()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        $metadata = ['category' => 'food', 'urgent' => true];
        
        $data = [
            'amount' => 75.00,
            'currency_code' => 'GHS',
            'purpose' => 'Emergency lunch',
            'metadata' => $metadata,
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
        $this->assertEquals($metadata, $paymentRequest->metadata);
        
        Event::assertDispatched(PaymentRequestCreated::class);
    }

    /** @test */
    public function it_creates_negotiable_payment_request_when_specified()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        $data = [
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Flexible payment',
            'description' => 'Amount can be adjusted',
            'is_negotiable' => true,
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
        $this->assertTrue($paymentRequest->is_negotiable);
        
        Event::assertDispatched(PaymentRequestCreated::class);
    }

    /** @test */
    public function it_creates_non_negotiable_payment_request_by_default()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        $data = [
            'amount' => 50.00,
            'currency_code' => 'GHS',
            'purpose' => 'Fixed payment',
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertInstanceOf(PaymentRequest::class, $paymentRequest);
        $this->assertFalse($paymentRequest->is_negotiable); // Default should be false
        
        Event::assertDispatched(PaymentRequestCreated::class);
    }

    /** @test */
    public function it_persists_payment_request_to_database()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        $data = [
            'amount' => 300.75,
            'currency_code' => 'GHS',
            'purpose' => 'Taxi fare',
            'description' => 'Round trip to downtown',
            'is_negotiable' => true,
        ];
        
        $action = new CreatePaymentRequest();
        $paymentRequest = $action->execute($user, $data);
        
        $this->assertDatabaseHas('payment_requests', [
            'id' => $paymentRequest->id,
            'user_id' => $user->id,
            'amount' => 300.75,
            'currency_code' => 'GHS',
            'purpose' => 'Taxi fare',
            'description' => 'Round trip to downtown',
            'is_negotiable' => true,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_rolls_back_transaction_on_failure()
    {
        Event::fake();
        
        $user = User::factory()->create();
        
        // Mock the action to throw an exception after creating the payment request
        $action = $this->getMockBuilder(CreatePaymentRequest::class)
                       ->onlyMethods([])
                       ->getMock();
        
        // We'll test that if an exception is thrown during the transaction,
        // everything gets rolled back. Let's create a scenario where the 
        // event dispatch fails
        Event::shouldReceive('dispatch')
             ->once()
             ->andThrow(new \Exception('Event dispatch failed'));
        
        $data = [
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Test transaction rollback',
        ];
        
        try {
            $action->execute($user, $data);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            // Verify that no payment request was created due to rollback
            $this->assertDatabaseMissing('payment_requests', [
                'user_id' => $user->id,
                'purpose' => 'Test transaction rollback',
            ]);
            $this->assertStringContainsString('Event dispatch failed', $e->getMessage());
        }
    }
}
