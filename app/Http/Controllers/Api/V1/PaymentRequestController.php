<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PaymentRequest\CreatePaymentRequest;
use App\Actions\PaymentRequest\UpdatePaymentRequest;
use App\Actions\PaymentRequest\DeletePaymentRequest;
use App\Actions\Payment\SettlePayment;
use App\Http\Filters\V1\PaymentRequestFilter;
use App\Http\Requests\Api\V1\StorePaymentRequestRequest;
use App\Http\Requests\Api\V1\UpdatePaymentRequestRequest;
use App\Http\Requests\Api\V1\ReplacePaymentRequestRequest;
use App\Http\Requests\Api\V1\SettlePaymentRequest;
use App\Http\Resources\V1\PaymentRequestResource;
use App\Http\Resources\V1\PaymentResource;
use App\Models\PaymentRequest;
use App\Policies\V1\PaymentRequestPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PaymentRequestController extends ApiController
{
    protected $policyClass = PaymentRequestPolicy::class;

    public function __construct(
        private readonly CreatePaymentRequest $createPaymentRequest,
        private readonly UpdatePaymentRequest $updatePaymentRequest,
        private readonly DeletePaymentRequest $deletePaymentRequest,
        private readonly SettlePayment $settlePayment
    ) {}

    /**
     * Get all payment requests
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * Returns a paginated list of payment requests for the authenticated user.
     * Supports sorting, filtering, and field selection using JSON:API conventions.
     * 
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=amount,-created_at
     * @queryParam filter[status] string Filter by status: pending, paid, cancelled, expired. Example: pending
     * @queryParam filter[amount][gte] numeric Filter by minimum amount. Example: 100
     * @queryParam filter[amount][lte] numeric Filter by maximum amount. Example: 500
     * @queryParam filter[purpose] string Filter by purpose. Wildcards are supported. Example: *lunch*
     * @queryParam filter[created_at][gte] date Filter by minimum creation date. Example: 2025-01-01
     * @queryParam include string Include related resources. Available: author,recipient. Example: author
     * @queryParam fields[paymentRequest] string Comma-separated list of fields to include. Example: amount,purpose,status
     * @queryParam per_page integer Number of results per page. Default is 15. Example: 10
     * @queryParam page integer Page number. Default is 1. Example: 1
     * 
     * @response status=200 scenario="Success" {
     *   "data": [
     *     {
     *       "type": "paymentRequest",
     *       "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
     *       "attributes": {
     *         "amount": "150.00",
     *         "formattedAmount": "GHS 150.00",
     *         "currencyCode": "GHS",
     *         "purpose": "Lunch payment",
     *         "isNegotiable": false,
     *         "status": "pending",
     *         "expiresAt": "2025-09-15T12:00:00Z",
     *         "paidAt": null,
     *         "isExpired": false,
     *         "createdAt": "2025-08-01T10:15:30Z",
     *         "updatedAt": "2025-08-01T10:15:30Z"
     *       },
     *       "relationships": {
     *         "author": {
     *           "data": {
     *             "type": "user",
     *             "id": 1
     *           },
     *           "links": {
     *             "self": "/api/v1/users/1"
     *           }
     *         }
     *       },
     *       "links": {
     *         "self": "/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *       }
     *     },
     *     {
     *       "type": "paymentRequest",
     *       "id": "a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11",
     *       "attributes": {
     *         "amount": "200.00",
     *         "formattedAmount": "GHS 200.00",
     *         "currencyCode": "GHS",
     *         "purpose": "Office supplies",
     *         "isNegotiable": false,
     *         "status": "paid",
     *         "expiresAt": null,
     *         "paidAt": "2025-08-01T09:20:15Z",
     *         "isExpired": false,
     *         "createdAt": "2025-07-28T14:30:45Z",
     *         "updatedAt": "2025-08-01T09:20:15Z"
     *       },
     *       "relationships": {
     *         "author": {
     *           "data": {
     *             "type": "user",
     *             "id": 2
     *           },
     *           "links": {
     *             "self": "/api/v1/users/2"
     *           }
     *         }
     *       },
     *       "links": {
     *         "self": "/api/v1/payment-requests/a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11"
     *       }
     *     }
     *   ],
     *   "links": {
     *     "first": "https://kitua.com/api/v1/payment-requests?page=1",
     *     "last": "https://kitua.com/api/v1/payment-requests?page=3",
     *     "prev": null,
     *     "next": "https://kitua.com/api/v1/payment-requests?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 3,
     *     "path": "https://kitua.com/api/v1/payment-requests",
     *     "per_page": 15,
     *     "to": 15,
     *     "total": 35
     *   }
     * }
     * 
     * @response status=401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated.",
     *   "status": 401
     * }
     */
    public function index(PaymentRequestFilter $filters)
    {
        $query = request()->user()->paymentRequests();
        
        // Apply filters, sorting, etc.
        $query = $query->filter($filters);
        
        // Handle includes
        if ($this->include('author')) {
            $query->with('user');
        }
        
        $perPage = request()->get('per_page', 15); // Default to 15 per page
        return PaymentRequestResource::collection($query->paginate($perPage));
    }

    /**
     * Create a payment request
     * 
     * Creates a new payment request record. Users can only create payment requests for themselves.
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @bodyParam amount numeric required The amount of the payment request. Example: 150
     * @bodyParam purpose string required The purpose of the payment request (max 100 chars). Example: Lunch payment
     * @bodyParam description string optional A longer description of the payment request. Example: Payment for team lunch at the cafeteria
     * @bodyParam image file optional An image to attach to the payment request (jpg, png, pdf). Maximum size: 5MB. No-example
     * @bodyParam expires_at datetime optional The date and time when the payment request expires. If not provided, defaults to 30 days from creation. Example: 2025-09-15T12:00:00Z
     * @bodyParam metadata object optional Additional metadata for the payment request. Example: {"restaurant":"Cafe Royal","receipt_number":"RCT-12345"}
     * @bodyParam negotiable boolean optional Whether the amount is negotiable. Default is false. Example: true
     * 
     * @response status=201 scenario="Created successfully" {
     *   "data": {
     *     "type": "paymentRequest",
     *     "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
     *     "attributes": {
     *       "amount": "150.00",
     *       "formattedAmount": "GHS 150.00",
     *       "currencyCode": "GHS",
     *       "purpose": "Lunch payment",
     *       "description": "Payment for team lunch at the cafeteria",
     *       "isNegotiable": false,
     *       "status": "pending",
     *       "expiresAt": "2025-09-15T12:00:00Z",
     *       "paidAt": null,
     *       "isExpired": false,
     *       "metadata": {
     *         "restaurant": "Cafe Royal",
     *         "receipt_number": "RCT-12345"
     *       },
     *       "createdAt": "2025-08-15T10:15:30Z",
     *       "updatedAt": "2025-08-15T10:15:30Z"
     *     },
     *     "relationships": {
     *       "author": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *     }
     *   }
     * }
     * 
     * @response status=422 scenario="Validation error" {
     *   "message": "Validation failed",
     *   "errors": {
     *     "amount": ["The amount field is required."],
     *     "purpose": ["The purpose field is required."]
     *   },
     *   "status": 422
     * }
     * 
     * @response status=401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated.",
     *   "status": 401
     * }
     */
    public function store(StorePaymentRequestRequest $request)
    {
        if ($this->isAble('store', PaymentRequest::class)) {
            $data = $request->getPaymentRequestData();
            
            // Add image if provided
            if ($request->hasImage()) {
                $data['image'] = $request->file('image');
            }
            
            $paymentRequest = $this->createPaymentRequest->execute(
                $request->user(),
                $data
            );
            
            return new PaymentRequestResource($paymentRequest);
        }

        return $this->notAuthorized('You are not authorized to create that resource');        
    }

    /**
     * Show a specific payment request
     * 
     * Display an individual payment request by its UUID. Users can only view their own payment requests.
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam uuid string required The UUID of the payment request. Example: f47ac10b-58cc-4372-a567-0e02b2c3d479
     * @queryParam include string Include related resources. Available: author. Example: author
     * 
     * @response status=200 scenario="Success" {
     *   "data": {
     *     "type": "paymentRequest",
     *     "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
     *     "attributes": {
     *       "amount": "150.00",
     *       "formattedAmount": "GHS 150.00",
     *       "currencyCode": "GHS",
     *       "purpose": "Lunch payment",
     *       "description": "Payment for team lunch",
     *       "isNegotiable": false,
     *       "status": "pending",
     *       "expiresAt": "2025-09-15T12:00:00Z",
     *       "paidAt": null,
     *       "isExpired": false,
     *       "metadata": {
     *         "restaurant": "Cafe Royal",
     *         "receipt_number": "RCT-12345"
     *       },
     *       "createdAt": "2025-08-01T10:15:30Z",
     *       "updatedAt": "2025-08-01T10:15:30Z"
     *     },
     *     "relationships": {
     *       "author": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *     }
     *   }
     * }
     * 
     * @response status=404 scenario="Not found" {
     *   "message": "Payment request not found",
     *   "status": 404
     * }
     * 
     * @response status=401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated.",
     *   "status": 401
     * }
     */
    public function show(PaymentRequest $uuid)
    {
        if (!$this->isAble('view', $uuid)) {
            return $this->notFound('Payment request not found');
        }
        
        if ($this->include('author')) {
            return new PaymentRequestResource($uuid->load('user'));
        }

        return new PaymentRequestResource($uuid);
    }

    /**
     * Update Payment Request
     * 
     * Partially update the specified payment request (PATCH method). Only provided fields will be updated.
     * Users can only update their own payment requests, and paid payment requests cannot be updated.
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam uuid string required The UUID of the payment request. Example: f47ac10b-58cc-4372-a567-0e02b2c3d479
     * @bodyParam amount numeric optional The amount of the payment request. Example: 200
     * @bodyParam purpose string optional The purpose of the payment request. Example: Updated lunch payment
     * @bodyParam description string optional A longer description of the payment request. Example: Updated payment for team lunch
     * @bodyParam image file optional A new image to attach to the payment request. No-example
     * @bodyParam remove_image boolean optional Whether to remove the existing image. Example: true
     * @bodyParam expires_at datetime optional The date and time when the payment request expires. Example: 2025-10-15T12:00:00Z
     * @bodyParam metadata object optional Additional metadata for the payment request. Example: {"restaurant":"Updated Cafe","receipt_number":"RCT-67890"}
     * @bodyParam negotiable boolean optional Whether the amount is negotiable. Example: true
     * 
     * @response status=200 scenario="Updated successfully" {
     *   "data": {
     *     "type": "paymentRequest",
     *     "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
     *     "attributes": {
     *       "amount": "200.00",
     *       "formattedAmount": "GHS 200.00",
     *       "currencyCode": "GHS",
     *       "purpose": "Updated lunch payment",
     *       "description": "Updated payment for team lunch",
     *       "isNegotiable": true,
     *       "status": "pending",
     *       "expiresAt": "2025-10-15T12:00:00Z",
     *       "paidAt": null,
     *       "isExpired": false,
     *       "metadata": {
     *         "restaurant": "Updated Cafe",
     *         "receipt_number": "RCT-67890"
     *       },
     *       "createdAt": "2025-08-01T10:15:30Z",
     *       "updatedAt": "2025-08-15T11:20:45Z"
     *     },
     *     "relationships": {
     *       "author": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *     }
     *   }
     * }
     * 
     * @response status=404 scenario="Not found" {
     *   "message": "Payment request not found",
     *   "status": 404
     * }
     * 
     * @response status=403 scenario="Paid payment request" {
     *   "message": "Cannot update a paid payment request",
     *   "status": 403
     * }
     * 
     * @response status=401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated.",
     *   "status": 401
     * }
     */
    public function update(UpdatePaymentRequestRequest $request, PaymentRequest $uuid)
    {
        // PATCH
        if ($this->isAble('update', $uuid)) {
            // Check if the payment request is paid before allowing update
            if ($uuid->status === 'paid') {
                return $this->error('Cannot update a paid payment request', 403);
            }
            
            $data = $request->mappedAttributes();
            $image = null;
            $removeImage = false;
            
            // Handle image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
            }
            
            // Check if we should remove existing image
            if ($request->input('remove_image')) {
                $removeImage = true;
            }
            
            try {
                $result = $this->updatePaymentRequest->execute(
                    $uuid, 
                    $request->user(), 
                    $data, 
                    $image, 
                    $removeImage
                );
                
                return new PaymentRequestResource($result);
            } catch (\Exception $e) {
                return $this->error('Failed to update payment request: ' . $e->getMessage(), 403);
            }
        }

        return $this->notFound('Payment request not found');
    }

    /**
     * Replace Payment Request
     * 
     * Completely replace the specified payment request (PUT method). All fields must be provided.
     * Users can only replace their own payment requests, and paid payment requests cannot be replaced.
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam uuid string required The UUID of the payment request. Example: f47ac10b-58cc-4372-a567-0e02b2c3d479
     * @bodyParam amount numeric required The amount of the payment request. Example: 250
     * @bodyParam purpose string required The purpose of the payment request. Example: Replaced lunch payment
     * @bodyParam description string required A longer description of the payment request. Example: Completely replaced payment for team lunch
     * @bodyParam expires_at datetime required The date and time when the payment request expires. Example: 2025-11-15T12:00:00Z
     * @bodyParam negotiable boolean required Whether the amount is negotiable. Example: false
     * 
     * @response status=200 scenario="Replaced successfully" {
     *   "data": {
     *     "type": "paymentRequest",
     *     "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
     *     "attributes": {
     *       "amount": "250.00",
     *       "formattedAmount": "GHS 250.00",
     *       "currencyCode": "GHS",
     *       "purpose": "Replaced lunch payment",
     *       "description": "Completely replaced payment for team lunch",
     *       "isNegotiable": false,
     *       "status": "pending",
     *       "expiresAt": "2025-11-15T12:00:00Z",
     *       "paidAt": null,
     *       "isExpired": false,
     *       "metadata": null,
     *       "createdAt": "2025-08-01T10:15:30Z",
     *       "updatedAt": "2025-08-15T12:30:15Z"
     *     },
     *     "relationships": {
     *       "author": {
     *         "data": {
     *           "type": "user",
     *           "id": 1
     *         },
     *         "links": {
     *           "self": "/api/v1/users/1"
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *     }
     *   }
     * }
     * 
     * @response status=404 scenario="Not found" {
     *   "message": "Payment request not found",
     *   "status": 404
     * }
     * 
     * @response status=422 scenario="Validation error" {
     *   "message": "Validation failed",
     *   "errors": {
     *     "amount": ["The amount field is required."],
     *     "purpose": ["The purpose field is required."]
     *   },
     *   "status": 422
     * }
     */
    public function replace(ReplacePaymentRequestRequest $request, PaymentRequest $uuid) 
    {
        // PUT
        if ($this->isAble('replace', $uuid)) {
            $uuid->update($request->mappedAttributes());
            return new PaymentRequestResource($uuid);
        }

        return $this->notFound('Payment request not found');
    }

    /**
     * Delete payment request
     * 
     * Remove the specified payment request from storage. Users can only delete their own payment requests,
     * and paid payment requests cannot be deleted.
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam uuid string required The UUID of the payment request. Example: f47ac10b-58cc-4372-a567-0e02b2c3d479
     * 
     * @response status=200 scenario="Deleted successfully" {
     *   "data": {
     *     "deleted_at": "2025-08-15T13:45:30Z"
     *   },
     *   "message": "Payment request deleted successfully",
     *   "status": 200
     * }
     * 
     * @response status=404 scenario="Not found" {
     *   "message": "Payment request not found",
     *   "status": 404
     * }
     * 
     * @response status=403 scenario="Paid payment request" {
     *   "message": "Failed to delete payment request: Cannot delete a paid payment request",
     *   "status": 403
     * }
     * 
     * @response status=401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated.",
     *   "status": 401
     * }
     */
    public function destroy(PaymentRequest $uuid)
    {
        // policy
        if ($this->isAble('delete', $uuid)) {
            try {
                $result = $this->deletePaymentRequest->execute($uuid, request()->user());
                
                if ($result) {
                return $this->success('Payment request deleted successfully', [
                    'deleted_at' => now()->toISOString()
                ]);
                }
                
                return $this->error('Failed to delete payment request', 403);
            } catch (\Exception $e) {
                return $this->error('Failed to delete payment request: ' . $e->getMessage(), 403);
            }
        }

        return $this->notFound('Payment request not found');
    }

    /**
     * Settle a payment request
     * 
     * Initiate payment for a specific payment request. This creates a payment record 
     * and initiates the payment process with the selected payment provider.
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam uuid string required The UUID of the payment request to settle. Example: f47ac10b-58cc-4372-a567-0e02b2c3d479
     * @bodyParam payment_method string required The payment method to use. Example: mobile_money
     * @bodyParam phone_number string required The phone number for mobile money payments. Example: +233201234567
     * @bodyParam amount numeric optional Custom amount for negotiable payment requests. Example: 120.50
     * @bodyParam callback_url string optional URL to redirect to after payment. Example: https://myapp.com/payment/callback
     * @bodyParam account_number string optional Account number for bank transfers. Example: 1234567890
     * @bodyParam metadata object optional Additional payment metadata. Example: {"note": "Payment for lunch"}
     * 
     * @response status=200 scenario="Settlement initiated successfully" {
     *   "data": {
     *     "type": "payment",
     *     "id": "9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f",
     *     "attributes": {
     *       "amount": "150.00",
     *       "formattedAmount": "GHS 150.00",
     *       "currencyCode": "GHS",
     *       "status": "processing",
     *       "paymentMethod": "mobile_money",
     *       "phoneNumber": "+233201234567",
     *       "provider": "dummy",
     *       "providerReference": "DUMMY_abc123_1692179400",
     *       "authorizationUrl": "https://dummy-payment.test/pay?reference=DUMMY_abc123_1692179400&payment_id=9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f",
     *       "accessCode": "dummy_access_92179400",
     *       "isCompleted": false,
     *       "isFailed": false,
     *       "isPending": true,
     *       "initiatedAt": "2025-08-16T08:30:00Z",
     *       "createdAt": "2025-08-16T08:30:00Z",
     *       "updatedAt": "2025-08-16T08:30:00Z"
     *     },
     *     "relationships": {
     *       "user": {
     *         "data": {
     *           "type": "user",
     *           "id": 2
     *         },
     *         "links": {
     *           "self": "/api/v1/users/2"
     *         }
     *       },
     *       "paymentRequest": {
     *         "data": {
     *           "type": "paymentRequest",
     *           "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *         },
     *         "links": {
     *           "self": "/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479"
     *         }
     *       }
     *     },
     *     "links": {
     *       "self": "/api/v1/payments/9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f"
     *     }
     *   },
     *   "message": "Payment settlement initiated successfully",
     *   "status": 200
     * }
     * 
     * @response status=422 scenario="Validation error" {
     *   "message": "Validation failed",
     *   "errors": {
     *     "payment_method": ["Payment method is required."],
     *     "phone_number": ["Phone number is required for mobile money payments."]
     *   },
     *   "status": 422
     * }
     * 
     * @response status=403 scenario="Cannot settle own payment request" {
     *   "message": "You cannot pay your own payment request",
     *   "status": 403
     * }
     * 
     * @response status=404 scenario="Payment request not found" {
     *   "message": "Payment request not found",
     *   "status": 404
     * }
     * 
     * @response status=410 scenario="Payment request expired" {
     *   "message": "Payment request has expired",
     *   "status": 410
     * }
     */
    public function settle(SettlePaymentRequest $request, PaymentRequest $uuid): JsonResponse
    {
        try {
            // Execute payment settlement
            $payment = $this->settlePayment->execute(
                $uuid,
                $request->user(),
                $request->getPaymentData()
            );

            $paymentResource = new PaymentResource($payment->load('user', 'payable'));
            
            return response()->json([
                'status' => 200,
                'message' => 'Payment settlement initiated successfully',
                'data' => $paymentResource->toArray(request())
            ], 200);

        } catch (ValidationException $e) {
            return $this->error(
                'Validation failed',
                422,
                $e->errors()
            );

        } catch (\Exception $e) {
            // Map exception codes to appropriate HTTP status codes
            $statusCode = 500; // Default to internal server error
            $message = $e->getMessage();
            
            // Handle database constraint violations
            if (str_contains($message, 'SQLSTATE[23000]')) {
                $statusCode = 422; // Unprocessable Entity for data validation errors
                $message = 'A payment already exists for this request';
            }
            
            return $this->error($message, $statusCode);
        }
    }
}
