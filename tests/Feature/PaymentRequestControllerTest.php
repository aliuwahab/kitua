<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\PaymentRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentRequestControllerTest extends TestCase
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
                'currency_symbol' => '₵',
                'currency_name' => 'Ghana Cedi',
                'is_active' => true,
            ]);
        }
    }

    /** @test */
    public function it_requires_authentication_to_access_payment_requests()
    {
        $response = $this->getJson('/api/v1/payment-requests');
        
        $response->assertStatus(401);
    }

    /** @test */
    public function it_lists_user_payment_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $userRequests = PaymentRequest::factory()->count(3)->create(['user_id' => $user->id]);
        
        // Create requests for another user (should not be returned)
        $otherUser = User::factory()->create();
        PaymentRequest::factory()->count(2)->create(['user_id' => $otherUser->id]);
        
        $response = $this->getJson('/api/v1/payment-requests');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'payment_requests' => [
                             'data' => [
                                 '*' => [
                                     'id',
                                     'amount',
                                     'currency_code',
                                     'purpose',
                                     'description',
                                     'is_negotiable',
                                     'status',
                                     'expires_at',
                                     'paid_at',
                                     'created_at',
                                     'updated_at',
                                     'media',
                                 ]
                             ],
                             'current_page',
                             'per_page',
                             'total',
                         ],
                     ],
                     'message'
                 ])
                 ->assertJsonCount(3, 'data.payment_requests.data');
    }

    /** @test */
    public function it_filters_payment_requests_by_status()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        PaymentRequest::factory()->count(2)->create(['user_id' => $user->id, 'status' => 'pending']);
        PaymentRequest::factory()->count(1)->paid()->create(['user_id' => $user->id]);
        PaymentRequest::factory()->count(1)->cancelled()->create(['user_id' => $user->id]);
        
        $response = $this->getJson('/api/v1/payment-requests?status=pending');
        
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data.payment_requests.data');
        
        foreach ($response->json('data.payment_requests.data') as $request) {
            $this->assertEquals('pending', $request['status']);
        }
    }

    /** @test */
    public function it_creates_a_payment_request_successfully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $data = [
            'amount' => 150.50,
            'currency_code' => 'GHS',
            'purpose' => 'Lunch money',
            'description' => 'Need money for lunch today',
            'expires_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ];
        
        $response = $this->postJson('/api/v1/payment-requests', $data);
        
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'payment_request' => [
                             'id',
                             'amount',
                             'currency_code',
                             'purpose',
                             'description',
                             'is_negotiable',
                             'status',
                             'expires_at',
                             'created_at',
                             'updated_at',
                             'media',
                         ],
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'status' => 201,
                     'data' => [
                         'payment_request' => [
                             'amount' => 150.50,
                             'currency_code' => 'GHS',
                             'purpose' => 'Lunch money',
                             'description' => 'Need money for lunch today',
                             'is_negotiable' => false,
                             'status' => 'pending',
                         ],
                     ],
                     'message' => 'Payment request created successfully'
                 ]);
        
        $this->assertDatabaseHas('payment_requests', [
            'user_id' => $user->id,
            'amount' => 150.50,
            'currency_code' => 'GHS',
            'purpose' => 'Lunch money',
            'description' => 'Need money for lunch today',
            'is_negotiable' => false,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_creates_payment_request_with_image()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        
        $data = [
            'amount' => 200.00,
            'currency_code' => 'GHS',
            'purpose' => 'Groceries',
            'description' => 'Grocery shopping receipt',
            'image' => $image,
        ];
        
        $response = $this->postJson('/api/v1/payment-requests', $data);
        
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'payment_request' => [
                             'id',
                             'amount',
                             'currency_code',
                             'purpose',
                             'description',
                             'status',
                             'media' => [
                                 '*' => [
                                     'id',
                                     'name',
                                     'file_name',
                                     'mime_type',
                                     'size',
                                     'original_url',
                                 ]
                             ],
                         ],
                     ],
                     'message'
                 ]);
        
        $paymentRequest = PaymentRequest::where('user_id', $user->id)->first();
        $this->assertCount(1, $paymentRequest->getMedia('images'));
    }

    /** @test */
    public function it_validates_payment_request_creation_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $response = $this->postJson('/api/v1/payment-requests', []);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['amount', 'currency_code', 'purpose']);
    }

    /** @test */
    public function it_shows_a_payment_request_by_uuid()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create(['user_id' => $user->id]);
        
        $response = $this->getJson("/api/v1/payment-requests/{$paymentRequest->id}");
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'payment_request' => [
                             'id',
                             'amount',
                             'currency_code',
                             'purpose',
                             'description',
                             'is_negotiable',
                             'status',
                             'expires_at',
                             'paid_at',
                             'created_at',
                             'updated_at',
                             'media',
                             'user' => [
                                 'id',
                                 'first_name',
                                 'surname',
                                 'full_name',
                             ],
                         ],
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'status' => 200,
                     'data' => [
                         'payment_request' => [
                             'id' => $paymentRequest->id,
                             'amount' => $paymentRequest->amount,
                             'purpose' => $paymentRequest->purpose,
                         ],
                     ],
                 ]);
    }

    /** @test */
    public function it_prevents_showing_other_users_payment_requests()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->getJson("/api/v1/payment-requests/{$paymentRequest->id}");
        
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_non_existent_payment_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $response = $this->getJson('/api/v1/payment-requests/non-existent-uuid');
        
        $response->assertStatus(404)
                 ->assertJson([
                     'status' => 404,
                     'message' => 'Payment request not found'
                 ]);
    }

    /** @test */
    public function it_updates_a_payment_request_successfully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Original purpose',
            'status' => 'pending',
        ]);
        
        $updateData = [
            'amount' => 200.00,
            'purpose' => 'Updated purpose',
            'description' => 'Updated description',
        ];
        
        $response = $this->putJson("/api/v1/payment-requests/{$paymentRequest->id}", $updateData);
        
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 200,
                     'data' => [
                         'payment_request' => [
                             'id' => $paymentRequest->id,
                             'amount' => 200.00,
                             'purpose' => 'Updated purpose',
                             'description' => 'Updated description',
                         ],
                     ],
                     'message' => 'Payment request updated successfully'
                 ]);
        
        $this->assertDatabaseHas('payment_requests', [
            'id' => $paymentRequest->id,
            'amount' => 200.00,
            'purpose' => 'Updated purpose',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function it_prevents_updating_paid_payment_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->paid()->create(['user_id' => $user->id]);
        
        $updateData = [
            'amount' => 200.00,
            'purpose' => 'Updated purpose',
        ];
        
        $response = $this->putJson("/api/v1/payment-requests/{$paymentRequest->id}", $updateData);
        
        $response->assertStatus(403)
                 ->assertJson([
                     'status' => 403,
                     'message' => 'Cannot update a paid payment request'
                 ]);
    }

    /** @test */
    public function it_prevents_updating_other_users_payment_requests()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create(['user_id' => $otherUser->id]);
        
        $updateData = [
            'purpose' => 'Hacked purpose',
        ];
        
        $response = $this->putJson("/api/v1/payment-requests/{$paymentRequest->id}", $updateData);
        
        $response->assertStatus(404);
    }

    /** @test */
    public function it_updates_payment_request_with_image()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        
        $image = UploadedFile::fake()->image('receipt.jpg', 800, 600);
        
        $updateData = [
            'description' => 'Added receipt image',
            'image' => $image,
        ];
        
        $response = $this->putJson("/api/v1/payment-requests/{$paymentRequest->id}", $updateData);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'payment_request' => [
                             'media' => [
                                 '*' => [
                                     'id',
                                     'name',
                                     'file_name',
                                     'mime_type',
                                     'size',
                                     'original_url',
                                 ]
                             ],
                         ],
                     ],
                 ]);
        
        $paymentRequest->refresh();
        $this->assertCount(1, $paymentRequest->getMedia('images'));
    }

    /** @test */
    public function it_deletes_a_payment_request_successfully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        
        $response = $this->deleteJson("/api/v1/payment-requests/{$paymentRequest->id}");
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'deleted_at',
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'status' => 200,
                     'message' => 'Payment request deleted successfully'
                 ]);
        
        $this->assertSoftDeleted('payment_requests', [
            'id' => $paymentRequest->id,
        ]);
    }

    /** @test */
    public function it_prevents_deleting_paid_payment_requests()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->paid()->create(['user_id' => $user->id]);
        
        $response = $this->deleteJson("/api/v1/payment-requests/{$paymentRequest->id}");
        
        $response->assertStatus(403)
                 ->assertJson([
                     'status' => 403,
                     'message' => 'Failed to delete payment request: Cannot delete a paid payment request.'
                 ]);
        
        $this->assertDatabaseHas('payment_requests', [
            'id' => $paymentRequest->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_prevents_deleting_other_users_payment_requests()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->deleteJson("/api/v1/payment-requests/{$paymentRequest->id}");
        
        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_validation_errors_gracefully()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $data = [
            'amount' => 'invalid',
            'currency_code' => '',
            'purpose' => '',
        ];
        
        $response = $this->postJson('/api/v1/payment-requests', $data);
        
        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'errors' => [
                         'amount',
                         'currency_code',
                         'purpose',
                     ]
                 ])
                 ->assertJson([
                     'status' => 422,
                 ]);
    }

    /** @test */
    public function it_validates_image_upload_properly()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        
        $data = [
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Test purpose',
            'image' => $invalidFile,
        ];
        
        $response = $this->postJson('/api/v1/payment-requests', $data);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['image']);
    }

    /** @test */
    public function it_creates_negotiable_payment_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $data = [
            'amount' => 100.00,
            'currency_code' => 'GHS',
            'purpose' => 'Negotiable payment',
            'description' => 'Amount can be negotiated',
            'is_negotiable' => true,
        ];
        
        $response = $this->postJson('/api/v1/payment-requests', $data);
        
        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 201,
                     'data' => [
                         'payment_request' => [
                             'amount' => 100.00,
                             'currency_code' => 'GHS',
                             'purpose' => 'Negotiable payment',
                             'description' => 'Amount can be negotiated',
                             'is_negotiable' => true,
                             'status' => 'pending',
                         ],
                     ],
                     'message' => 'Payment request created successfully'
                 ]);
        
        $this->assertDatabaseHas('payment_requests', [
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Negotiable payment',
            'is_negotiable' => true,
        ]);
    }

    /** @test */
    public function it_updates_negotiable_status()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $paymentRequest = PaymentRequest::factory()->create([
            'user_id' => $user->id,
            'amount' => 100.00,
            'purpose' => 'Fixed amount initially',
            'is_negotiable' => false,
            'status' => 'pending',
        ]);
        
        $updateData = [
            'is_negotiable' => true,
            'description' => 'Now negotiable',
        ];
        
        $response = $this->putJson("/api/v1/payment-requests/{$paymentRequest->id}", $updateData);
        
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 200,
                     'data' => [
                         'payment_request' => [
                             'id' => $paymentRequest->id,
                             'amount' => 100.00,
                             'purpose' => 'Fixed amount initially',
                             'description' => 'Now negotiable',
                             'is_negotiable' => true,
                         ],
                     ],
                     'message' => 'Payment request updated successfully'
                 ]);
        
        $this->assertDatabaseHas('payment_requests', [
            'id' => $paymentRequest->id,
            'is_negotiable' => true,
            'description' => 'Now negotiable',
        ]);
    }

    /** @test */
    public function it_paginates_payment_requests_correctly()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        PaymentRequest::factory()->count(15)->create(['user_id' => $user->id]);
        
        $response = $this->getJson('/api/v1/payment-requests?per_page=5');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'payment_requests' => [
                             'data',
                             'current_page',
                             'per_page',
                             'total',
                             'last_page',
                         ]
                     ]
                 ])
                 ->assertJsonCount(5, 'data.payment_requests.data')
                 ->assertJson([
                     'data' => [
                         'payment_requests' => [
                             'per_page' => 5,
                             'total' => 15,
                             'last_page' => 3,
                         ]
                     ]
                 ]);
    }
}
