<?php

namespace App\Actions\Auth;

use App\Events\Auth\UserLoggedOut;
use App\Models\User;
use App\Models\DeviceSession;
use Illuminate\Http\Request;

class LogoutUser
{
    public function execute(Request $request, string $reason = 'user_initiated'): array
    {
        $user = $request->user();
        $token = $request->user()->currentAccessToken();
        
        // Find the device session associated with this token/request
        $deviceInfo = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => 'unknown', // Default for web/unknown
        ];
        
        $fingerprint = DeviceSession::generateFingerprint($deviceInfo);
        
        $deviceSession = DeviceSession::where('user_id', $user->id)
            ->where('device_fingerprint', $fingerprint)
            ->first();

        // If no device session found by fingerprint, try to get any active session
        if (!$deviceSession) {
            $deviceSession = $user->activeDeviceSessions()->first();
        }

        // Delete the current access token
        $token->delete();

        // Update device session if found
        if ($deviceSession) {
            $deviceSession->updateActivity();
            
            // If this is a forced logout or security reason, revoke the device
            if (in_array($reason, ['security', 'admin_revoke', 'suspicious_activity'])) {
                $deviceSession->revoke();
            }
        }

        // Emit logout event (even if no device session found)
        UserLoggedOut::dispatch($user, $deviceSession, $reason);

        return [
            'user' => $user,
            'device_session' => $deviceSession,
            'reason' => $reason,
            'logged_out_at' => now(),
        ];
    }

    /**
     * Logout from all devices
     */
    public function logoutFromAllDevices(User $user, string $reason = 'user_initiated'): array
    {
        // Get all active device sessions
        $deviceSessions = $user->activeDeviceSessions()->get();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Update all device sessions
        foreach ($deviceSessions as $deviceSession) {
            $deviceSession->updateActivity();
            
            if (in_array($reason, ['security', 'admin_revoke', 'suspicious_activity'])) {
                $deviceSession->revoke();
            }
            
            // Emit logout event for each device
            UserLoggedOut::dispatch($user, $deviceSession, $reason);
        }

        return [
            'user' => $user,
            'device_sessions_count' => $deviceSessions->count(),
            'reason' => $reason,
            'logged_out_at' => now(),
        ];
    }
}
