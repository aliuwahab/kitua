<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PaymentRequest\CreatePaymentRequest;
use App\Actions\PaymentRequest\UpdatePaymentRequest;
use App\Actions\PaymentRequest\DeletePaymentRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PaymentRequest\CreatePaymentRequestRequest;
use App\Http\Requests\Api\V1\PaymentRequest\UpdatePaymentRequestRequest;
use App\Models\PaymentRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentRequestController extends Controller
{
    use ApiResponses;

    public function __construct(
        private CreatePaymentRequest $createPaymentRequest,
        private UpdatePaymentRequest $updatePaymentRequest,
        private DeletePaymentRequest $deletePaymentRequest
    ) {}

    /**
     * Display a listing of payment requests for the authenticated user
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @queryParam status string Filter by status (pending, paid, cancelled, expired). Example: pending
     * @queryParam page integer Page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (max 50). Example: 15
     * 
     * @response 200 {
     *   "data": {
     *     "payment_requests": {
     *       "current_page": 1,
     *       "data": [
     *         {
     *           "id": 1,
     *           "uuid": "123e4567-e89b-12d3-a456-426614174000",
     *           "amount": "150.50",
     *           "currency_code": "GHS",
     *           "formatted_amount": "GHS 150.50",
     *           "purpose": "Lunch money",
     *           "description": "Need money for lunch today",
     *           "status": "pending",
     *           "expires_at": "2025-08-20T18:00:00Z",
     *           "is_expired": false,
     *           "created_at": "2025-08-15T10:00:00Z",
     *           "media": []
     *         }
     *       ],
     *       "total": 1,
     *       "per_page": 15,
     *       "current_page": 1
     *     }
     *   },
     *   "message": "Payment requests retrieved successfully",
     *   "status": 200
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->paymentRequests()
                          ->with('media')
                          ->latest();

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = min($request->get('per_page', 15), 50);
        $paymentRequests = $query->paginate($perPage);

        return $this->ok('Payment requests retrieved successfully', [
            'payment_requests' => $paymentRequests
        ]);
    }

    /**
     * Store a newly created payment request
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @response 201 {
     *   "data": {
     *     "payment_request": {
     *       "id": 1,
     *       "uuid": "123e4567-e89b-12d3-a456-426614174000",
     *       "amount": "150.50",
     *       "currency_code": "GHS",
     *       "formatted_amount": "GHS 150.50",
     *       "purpose": "Lunch money",
     *       "description": "Need money for lunch today",
     *       "status": "pending",
     *       "expires_at": "2025-08-20T18:00:00Z",
     *       "is_expired": false,
     *       "created_at": "2025-08-15T10:00:00Z",
     *       "media": []
     *     }
     *   },
     *   "message": "Payment request created successfully",
     *   "status": 201
     * }
     */
    public function store(CreatePaymentRequestRequest $request): JsonResponse
    {
        try {
            $data = $request->getPaymentRequestData();
            
            // Add image to data if provided
            if ($request->hasImage()) {
                $data['image'] = $request->file('image');
            }
            
            $paymentRequest = $this->createPaymentRequest->execute(
                $request->user(),
                $data
            );

            return $this->created('Payment request created successfully', [
                'payment_request' => $paymentRequest
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to create payment request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified payment request
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam payment_request required The UUID of the payment request. Example: 123e4567-e89b-12d3-a456-426614174000
     * 
     * @response 200 {
     *   "data": {
     *     "payment_request": {
     *       "id": 1,
     *       "uuid": "123e4567-e89b-12d3-a456-426614174000",
     *       "amount": "150.50",
     *       "currency_code": "GHS",
     *       "formatted_amount": "GHS 150.50",
     *       "purpose": "Lunch money",
     *       "description": "Need money for lunch today",
     *       "status": "pending",
     *       "expires_at": "2025-08-20T18:00:00Z",
     *       "is_expired": false,
     *       "created_at": "2025-08-15T10:00:00Z",
     *       "user": {
     *         "id": 1,
     *         "first_name": "John",
     *         "surname": "Doe",
     *         "full_name": "John Doe"
     *       },
     *       "media": []
     *     }
     *   },
     *   "message": "Payment request retrieved successfully",
     *   "status": 200
     * }
     * 
     * @response 404 {
     *   "message": "Payment request not found",
     *   "status": 404
     * }
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        try {
            $paymentRequest = $request->user()->paymentRequests()
                                    ->with(['media', 'user:id,first_name,surname'])
                                    ->findOrFail($uuid);

            return $this->ok('Payment request retrieved successfully', [
                'payment_request' => $paymentRequest
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Payment request not found');
        }
    }

    /**
     * Update the specified payment request
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam payment_request required The UUID of the payment request. Example: 123e4567-e89b-12d3-a456-426614174000
     * 
     * @response 200 {
     *   "data": {
     *     "payment_request": {
     *       "id": 1,
     *       "uuid": "123e4567-e89b-12d3-a456-426614174000",
     *       "amount": "200.00",
     *       "currency_code": "GHS",
     *       "formatted_amount": "GHS 200.00",
     *       "purpose": "Dinner money",
     *       "description": "Updated - need money for dinner",
     *       "status": "pending",
     *       "expires_at": "2025-08-22T20:00:00Z",
     *       "is_expired": false,
     *       "updated_at": "2025-08-15T11:00:00Z",
     *       "media": []
     *     }
     *   },
     *   "message": "Payment request updated successfully",
     *   "status": 200
     * }
     * 
     * @response 403 {
     *   "message": "Cannot update a paid payment request",
     *   "status": 403
     * }
     */
    public function update(UpdatePaymentRequestRequest $request, string $uuid): JsonResponse
    {
        try {
            $paymentRequest = $request->user()->paymentRequests()
                                    ->findOrFail($uuid);

            // Check if payment request can be updated
            if ($paymentRequest->status === 'paid') {
                return $this->error('Cannot update a paid payment request', 403);
            }

            $data = $request->getPaymentRequestData();
            
            $updatedPaymentRequest = $this->updatePaymentRequest->execute(
                $paymentRequest,
                $request->user(),
                $data,
                $request->hasImage() ? $request->file('image') : null,
                $request->shouldRemoveImage()
            );

            return $this->ok('Payment request updated successfully', [
                'payment_request' => $updatedPaymentRequest
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Payment request not found');
        } catch (\Exception $e) {
            return $this->error('Failed to update payment request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified payment request
     * 
     * @group Payment Requests
     * @authenticated
     * 
     * @urlParam payment_request required The UUID of the payment request. Example: 123e4567-e89b-12d3-a456-426614174000
     * 
     * @response 200 {
     *   "data": {
     *     "deleted_at": "2025-08-15T12:00:00Z"
     *   },
     *   "message": "Payment request deleted successfully",
     *   "status": 200
     * }
     * 
     * @response 403 {
     *   "message": "Cannot delete a paid payment request",
     *   "status": 403
     * }
     */
    public function destroy(Request $request, string $uuid): JsonResponse
    {
        try {
            $paymentRequest = $request->user()->paymentRequests()
                                    ->findOrFail($uuid);

            $this->deletePaymentRequest->execute($paymentRequest, $request->user());

            return $this->ok('Payment request deleted successfully', [
                'deleted_at' => now()->toISOString()
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Payment request not found');
        } catch (\Exception $e) {
            return $this->error('Failed to delete payment request: ' . $e->getMessage(), 403);
        }
    }
}
