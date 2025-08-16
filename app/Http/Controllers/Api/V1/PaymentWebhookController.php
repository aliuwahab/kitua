<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Payment\SettlePayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private SettlePayment $settlePayment
    ) {}

    /**
     * Handle payment webhook from providers.
     * 
     * This endpoint receives webhook notifications from payment providers
     * when payment status changes occur.
     * 
     * @group Payment Webhooks
     * 
     * @urlParam provider string required The payment provider name (dummy, paystack, flutterwave, etc.). Example: dummy
     * 
     * @response status=200 scenario="Webhook processed successfully" {
     *   "status": "success",
     *   "message": "Webhook processed successfully"
     * }
     * 
     * @response status=400 scenario="Invalid webhook data" {
     *   "status": "error",
     *   "message": "Invalid webhook data"
     * }
     * 
     * @response status=404 scenario="Unknown provider" {
     *   "status": "error",
     *   "message": "Unknown payment provider"
     * }
     */
    public function handle(Request $request, string $provider): JsonResponse
    {
        try {
            // Log incoming webhook
            Log::info('Webhook received', [
                'provider' => $provider,
                'payload' => $request->all(),
                'headers' => $this->getRelevantHeaders($request),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            // Validate provider exists
            if (!$this->isValidProvider($provider)) {
                Log::warning('Webhook received for unknown provider', [
                    'provider' => $provider,
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unknown payment provider'
                ], 404);
            }

            // Get webhook payload and headers
            $payload = $request->all();
            $headers = $this->getRelevantHeaders($request);

            // Basic validation
            if (empty($payload)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Empty webhook payload'
                ], 400);
            }

            // Process the webhook
            $payment = $this->settlePayment->processWebhookCallback($provider, $payload, $headers);

            if ($payment) {
                Log::info('Webhook processed successfully', [
                    'provider' => $provider,
                    'payment_id' => $payment->id,
                    'status' => $payment->status
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook processed successfully',
                    'payment_id' => $payment->id
                ]);
            } else {
                // Webhook was processed but no payment was found/updated
                Log::info('Webhook processed but no payment updated', [
                    'provider' => $provider
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook processed'
                ]);
            }

        } catch (ValidationException $e) {
            Log::warning('Webhook validation failed', [
                'provider' => $provider,
                'errors' => $e->errors(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook data',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle specific webhook events (if providers support it).
     * 
     * Some providers might send different webhook URLs for different events.
     * This method handles event-specific webhooks.
     * 
     * @group Payment Webhooks
     * 
     * @urlParam provider string required The payment provider name. Example: paystack
     * @urlParam event string required The event type (payment.success, payment.failed, etc.). Example: payment.success
     */
    public function handleEvent(Request $request, string $provider, string $event): JsonResponse
    {
        Log::info('Event-specific webhook received', [
            'provider' => $provider,
            'event' => $event,
            'payload' => $request->all()
        ]);

        // For now, treat event-specific webhooks the same as general webhooks
        // In the future, we might want to handle specific events differently
        return $this->handle($request, $provider);
    }

    /**
     * Get relevant headers for webhook validation.
     */
    private function getRelevantHeaders(Request $request): array
    {
        $headers = [];
        
        // Common webhook signature headers
        $signatureHeaders = [
            'X-Signature',
            'X-Hub-Signature',
            'X-Webhook-Signature',
            'X-Paystack-Signature',
            'X-Flutterwave-Signature',
            'X-Dummy-Signature',
            'Authorization'
        ];

        foreach ($signatureHeaders as $header) {
            $value = $request->header($header);
            if ($value) {
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Check if the provider is valid.
     */
    private function isValidProvider(string $provider): bool
    {
        $validProviders = [
            'dummy',
            'mtn_momo',
            'paystack',
            'flutterwave',
            'stripe',
            'razorpay'
        ];

        return in_array(strtolower($provider), $validProviders);
    }

    /**
     * Test webhook endpoint for development.
     * This endpoint can be used to test webhook processing during development.
     * 
     * @group Payment Webhooks
     * 
     * @urlParam provider string required The payment provider name. Example: dummy
     * 
     * @bodyParam reference string required The payment reference to test. Example: DUMMY_123_456
     * @bodyParam status string required The payment status to simulate. Example: success
     * @bodyParam amount numeric The payment amount. Example: 100.50
     * @bodyParam currency string The currency code. Example: GHS
     */
    public function test(Request $request, string $provider): JsonResponse
    {
        if (!app()->environment(['local', 'testing'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test endpoint only available in development'
            ], 403);
        }

        $request->validate([
            'reference' => 'required|string',
            'status' => 'required|string|in:success,failed,pending',
            'amount' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3'
        ]);

        $testPayload = [
            'reference' => $request->input('reference'),
            'status' => $request->input('status'),
            'amount' => $request->input('amount', 100),
            'currency' => $request->input('currency', 'GHS'),
            'gateway_response' => 'Test webhook',
            'channel' => 'test',
            'paid_at' => now()->toISOString()
        ];

        $testHeaders = [
            'X-' . ucfirst($provider) . '-Signature' => 'test_signature_' . time()
        ];

        Log::info('Processing test webhook', [
            'provider' => $provider,
            'payload' => $testPayload
        ]);

        try {
            $payment = $this->settlePayment->processWebhookCallback($provider, $testPayload, $testHeaders);

            return response()->json([
                'status' => 'success',
                'message' => 'Test webhook processed',
                'payment_id' => $payment?->id,
                'payment_status' => $payment?->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test webhook failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
