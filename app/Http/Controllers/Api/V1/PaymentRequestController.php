<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PaymentRequest\CreatePaymentRequest;
use App\Actions\PaymentRequest\UpdatePaymentRequest;
use App\Actions\PaymentRequest\DeletePaymentRequest;
use App\Http\Filters\V1\PaymentRequestFilter;
use App\Http\Requests\Api\V1\StorePaymentRequestRequest;
use App\Http\Requests\Api\V1\UpdatePaymentRequestRequest;
use App\Http\Requests\Api\V1\ReplacePaymentRequestRequest;
use App\Http\Resources\V1\PaymentRequestResource;
use App\Models\PaymentRequest;
use App\Policies\V1\PaymentRequestPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentRequestController extends ApiController
{
    protected $policyClass = PaymentRequestPolicy::class;

    public function __construct(
        private readonly CreatePaymentRequest $createPaymentRequest,
        private readonly UpdatePaymentRequest $updatePaymentRequest,
        private readonly DeletePaymentRequest $deletePaymentRequest
    ) {}

    /**
     * Get all payment requests
     * 
     * @group Payment Requests
     * @authenticated
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=amount,-createdAt
     * @queryParam filter[status] Filter by status: pending, paid, cancelled, expired. No-example
     * @queryParam filter[amount][gte] Filter by minimum amount. Example: 100
     * @queryParam filter[purpose] Filter by purpose. Wildcards are supported. Example: *lunch*
     * @queryParam include string Include related resources. Example: author
     * @queryParam fields[paymentRequest] string Comma-separated list of fields to include. Example: amount,purpose,status
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
     * Creates a new payment request record. Users can only create payment requests for themselves. Admins can create for any user.
     * 
     * @group Payment Requests
     * 
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
     * Show a specific payment request.
     * 
     * Display an individual payment request.
     * 
     * @group Payment Requests
     * 
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
     * Update the specified payment request in storage (PATCH).
     * 
     * @group Payment Requests
     * 
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
     * Replace the specified payment request in storage (PUT).
     * 
     * @group Payment Requests
     * 
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
     * Delete payment request.
     * 
     * Remove the specified resource from storage.
     * 
     * @group Payment Requests
     * 
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
}
