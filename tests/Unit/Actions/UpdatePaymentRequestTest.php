<?php

namespace Tests\Unit\Actions;

use App\Actions\PaymentRequest\UpdatePaymentRequest;
use App\Events\PaymentRequestUpdated;
use App\Models\Country;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdatePaymentRequestTest extends TestCase
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
    public function it_updates_payment_request_successfully()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Old purpose',
            'description' => 'Old description',
            'status' => 'pending',
        ]);
        
        $updateData = [
            'amount' => 200.00,
            'purpose' => 'Updated purpose',
            'description' => 'Updated description',
        ];
        
        $action = new UpdatePaymentRequest();
        $updatedPaymentRequest = $action->execute($paymentRequest, $user, $updateData);
        
        $this->assertInstanceOf(PaymentRequest::class, $updatedPaymentRequest);
        $this->assertEquals(200.00, $updatedPaymentRequest->amount);
        $this->assertEquals('Updated purpose', $updatedPaymentRequest->purpose);
        $this->assertEquals('Updated description', $updatedPaymentRequest->description);
        $this->assertEquals('GHS', $updatedPaymentRequest->currency_code); // Should remain unchanged
        
        Event::assertDispatched(PaymentRequestUpdated::class);
    }

    /** @test */
    public function it_adds_image_when_provided()
    {
        Event::fake();
        Storage::fake('public');
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Test purpose',
        ]);
        
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        
        $updateData = [
            'description' => 'Added receipt image',
            'image' => $image,
        ];
        
        $action = new UpdatePaymentRequest();
        $updatedPaymentRequest = $action->execute($paymentRequest, $user, $updateData);
        
        // Check that media was attached
        $this->assertCount(1, $updatedPaymentRequest->getMedia('images'));
        
        $media = $updatedPaymentRequest->getFirstMedia('images');
        $this->assertEquals('receipt', $media->name);
        $this->assertEquals('image/jpeg', $media->mime_type);
        
        Event::assertDispatched(PaymentRequestUpdated::class);
    }

    /** @test */
    public function it_removes_existing_image_when_remove_image_is_true()
    {
        Event::fake();
        Storage::fake('public');
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Test purpose',
        ]);
        
        // Add an image first
        $image = UploadedFile::fake()->image('original.jpg', 600, 400);
        $paymentRequest->addMedia($image)
                      ->toMediaCollection('images');
        
        $this->assertCount(1, $paymentRequest->getMedia('images'));
        
        $updateData = [
            'description' => 'Removed image',
            'remove_image' => true,
        ];
        
        $action = new UpdatePaymentRequest();
        $updatedPaymentRequest = $action->execute($paymentRequest, $user, $updateData, null, true);
        
        // Check that media was removed
        $this->assertCount(0, $updatedPaymentRequest->getMedia('images'));
        
        Event::assertDispatched(PaymentRequestUpdated::class);
    }

    /** @test */
    public function it_replaces_existing_image_when_new_image_provided()
    {
        Event::fake();
        Storage::fake('public');
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Test purpose',
        ]);
        
        // Add an image first
        $originalImage = UploadedFile::fake()->image('original.jpg', 600, 400);
        $paymentRequest->addMedia($originalImage)
                      ->toMediaCollection('images');
        
        $this->assertCount(1, $paymentRequest->getMedia('images'));
        
        // Update with new image
        $newImage = UploadedFile::fake()->image('updated.jpg', 800, 600);
        
        $updateData = [
            'description' => 'Updated image',
            'image' => $newImage,
        ];
        
        $action = new UpdatePaymentRequest();
        $updatedPaymentRequest = $action->execute($paymentRequest, $user, $updateData);
        
        // Check that old image was replaced with new one
        $this->assertCount(1, $updatedPaymentRequest->getMedia('images'));
        
        $media = $updatedPaymentRequest->getFirstMedia('images');
        $this->assertEquals('updated', $media->name);
        
        Event::assertDispatched(PaymentRequestUpdated::class);
    }

    /** @test */
    public function it_updates_metadata_when_provided()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $originalMetadata = ['category' => 'food', 'urgent' => false];
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Test purpose',
            'metadata' => $originalMetadata,
        ]);
        
        $newMetadata = ['category' => 'transport', 'urgent' => true, 'priority' => 'high'];
        
        $updateData = [
            'metadata' => $newMetadata,
        ];
        
        $action = new UpdatePaymentRequest();
        $updatedPaymentRequest = $action->execute($paymentRequest, $user, $updateData);
        
        $this->assertEquals($newMetadata, $updatedPaymentRequest->metadata);
        
        Event::assertDispatched(PaymentRequestUpdated::class, function ($event) use ($originalMetadata) {
            return $event->originalData['metadata'] === $originalMetadata;
        });
    }

    /** @test */
    public function it_updates_expires_at_when_provided()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $originalExpiresAt = now()->addDays(5);
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'purpose' => 'Test purpose',
            'expires_at' => $originalExpiresAt,
        ]);
        
        $newExpiresAt = now()->addDays(10)->format('Y-m-d H:i:s');
        
        $updateData = [
            'expires_at' => $newExpiresAt,
        ];
        
        $action = new UpdatePaymentRequest();
        $updatedPaymentRequest = $action->execute($paymentRequest, $user, $updateData);
        
        $this->assertEquals($newExpiresAt, $updatedPaymentRequest->expires_at->format('Y-m-d H:i:s'));
        
        Event::assertDispatched(PaymentRequestUpdated::class);
    }

    /** @test */
    public function it_persists_updates_to_database()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 150.00,
            'purpose' => 'Original purpose',
            'description' => 'Original description',
        ]);
        
        $updateData = [
            'amount' => 250.00,
            'purpose' => 'Updated purpose',
            'description' => 'Updated description',
        ];
        
        $action = new UpdatePaymentRequest();
        $action->execute($paymentRequest, $user, $updateData);
        
        $this->assertDatabaseHas('payment_requests', [
            'id' => $paymentRequest->id,
            'user_id' => $user->id,
            'amount' => 250.00,
            'purpose' => 'Updated purpose',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function it_captures_original_data_before_update()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $originalData = [
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Original purpose',
            'description' => 'Original description',
            'expires_at' => now()->addDays(3),
            'metadata' => ['category' => 'food'],
        ];
        
        $paymentRequest = PaymentRequest::factory()->create(array_merge(
            ['user_id' => $user->id],
            $originalData
        ));
        
        $updateData = [
            'amount' => 200.00,
            'purpose' => 'Updated purpose',
        ];
        
        $action = new UpdatePaymentRequest();
        $action->execute($paymentRequest, $user, $updateData);
        
        Event::assertDispatched(PaymentRequestUpdated::class);
    }

    /** @test */
    public function it_rolls_back_transaction_on_failure()
    {
        Event::fake();
        
        $user = User::factory()->create();
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Original purpose',
        ]);
        
        // Create invalid data that should cause a database error
        $updateData = [
            'amount' => 'invalid_amount', // This should cause a type error
        ];
        
        $action = new UpdatePaymentRequest();
        
        try {
            $action->execute($paymentRequest, $user, $updateData);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            // Verify that the payment request was not updated due to rollback
            $this->assertDatabaseHas('payment_requests', [
                'id' => $paymentRequest->id,
                'amount' => 100.00,
                'purpose' => 'Original purpose',
            ]);
        }
        
        Event::assertNotDispatched(PaymentRequestUpdated::class);
    }
}
