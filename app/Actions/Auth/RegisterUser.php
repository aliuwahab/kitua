<?php

namespace App\Actions\Auth;

use App\Events\Auth\UserRegistered;
use App\Events\Auth\PaymentAccountCreated;
use App\Events\Auth\DeviceRegistered;
use App\Models\User;
use App\Models\PaymentAccount;
use App\Models\DeviceSession;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterUser
{
    /**
     * Initiate registration/login flow - generate and send PIN via SMS
     */
    public function initiate(array $userData, array $deviceInfo): array
    {
        $mobileNumber = $userData['mobile_number'];
        $existingUser = User::where('mobile_number', $mobileNumber)->first();
        
        // Generate a 6-digit PIN
        $pin = $this->generatePin();
        $pinHash = bcrypt($pin);
        
        if ($existingUser) {
            // User exists - update their PIN and send SMS for login
            $existingUser->update([
                'pin' => $pinHash,
                'mobile_verified_at' => null, // Reset verification until PIN is confirmed
            ]);
            
            // TODO: Send SMS with PIN
            // $this->sendSMS($mobileNumber, "Your Kitua login PIN is: {$pin}");
            
            return [
                'user_exists' => true,
                'mobile_number' => $mobileNumber,
                'message' => 'Login PIN sent to your mobile number',
                'pin' => $pin, // TODO: Remove this in production - only for testing
            ];
        } else {
            // New user - create with pending verification
            return DB::transaction(function () use ($userData, $deviceInfo, $pinHash, $pin, $mobileNumber) {
                // Get Ghana as default country, or create if doesn't exist
                $country = Country::firstOrCreate(
                    ['code' => 'GH'],
                    [
                        'name' => 'Ghana',
                        'currency_code' => 'GHS',
                        'currency_symbol' => 'GH₵',
                        'currency_name' => 'Ghana Cedi',
                        'is_active' => true,
                    ]
                );
                
                $user = User::create([
                    'mobile_number' => $mobileNumber,
                    'first_name' => $userData['first_name'],
                    'surname' => $userData['surname'],
                    'other_names' => $userData['other_names'] ?? null,
                    'pin' => $pinHash,
                    'user_type' => 'customer',
                    'is_active' => false, // Will be activated after PIN verification
                    'mobile_verified_at' => null, // Explicitly set to null until PIN verification
                    'country_id' => $userData['country_id'] ?? $country->id,
                ]);

                // Create primary payment account
                $paymentAccount = PaymentAccount::create([
                    'user_id' => $user->id,
                    'account_type' => 'momo',
                    'account_number' => $mobileNumber,
                    'account_name' => $user->full_name,
                    'provider' => $userData['provider'] ?? null,
                    'is_primary' => true,
                    'is_verified' => false,
                ]);

                // TODO: Send SMS with PIN
                // $this->sendSMS($mobileNumber, "Welcome to Kitua! Your registration PIN is: {$pin}");

                return [
                    'user_exists' => false,
                    'mobile_number' => $mobileNumber,
                    'message' => 'Registration PIN sent to your mobile number',
                    'pin' => $pin, // TODO: Remove this in production - only for testing
                ];
            });
        }
    }

    /**
     * Complete registration/login by verifying PIN
     */
    public function complete(string $mobileNumber, string $pin, array $deviceInfo): array
    {
        $user = User::where('mobile_number', $mobileNumber)->first();
        
        if (!$user || !password_verify($pin, $user->pin)) {
            throw new \Exception('Invalid PIN or mobile number');
        }

        return DB::transaction(function () use ($user, $deviceInfo) {
            // Check if user was just created (never verified before) - BEFORE updating
            // A user is "new" if they have never been verified, regardless of current status
            $isNewUser = $user->mobile_verified_at === null && !$user->is_active;

            // Activate user and mark mobile as verified
            $user->update([
                'is_active' => true,
                'mobile_verified_at' => now(),
            ]);

            // Create/update device session
            $deviceSession = DeviceSession::createOrUpdateSession($user->id, array_merge($deviceInfo, [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]));

            $isNewDevice = $deviceSession->wasRecentlyCreated;

            // Revoke existing tokens to ensure single session per device
            $user->tokens()->delete();

            // Create authentication token (no expiry for mobile banking)
            $token = $user->createToken('mobile_app_token');

            // Emit events
            if ($isNewUser) {
                $paymentAccount = $user->paymentAccounts()->first();
                UserRegistered::dispatch($user, $paymentAccount, $deviceSession, $deviceInfo);
                PaymentAccountCreated::dispatch($user, $paymentAccount, true);
            }
            
            DeviceRegistered::dispatch($user, $deviceSession, $isNewDevice);

            return [
                'user' => $user->fresh()->load(['paymentAccounts', 'deviceSessions']),
                'device_session' => $deviceSession,
                'token' => $token->plainTextToken,
                'is_new_user' => $isNewUser,
                'is_new_device' => $isNewDevice,
            ];
        });
    }

    /**
     * Generate a 6-digit PIN
     */
    private function generatePin(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send SMS (placeholder - integrate with SMS provider)
     */
    private function sendSMS(string $mobileNumber, string $message): void
    {
        // TODO: Integrate with SMS provider (e.g., Twilio, AfricasTalking, etc.)
        // For now, we'll just log it
        logger("SMS to {$mobileNumber}: {$message}");
    }
}
