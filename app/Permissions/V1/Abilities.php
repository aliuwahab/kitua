<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities
{
    // Payment Request abilities (following masterclass convention)
    public const CreatePaymentRequest = 'payment-request:create';
    public const UpdatePaymentRequest = 'payment-request:update';
    public const ReplacePaymentRequest = 'payment-request:replace';
    public const DeletePaymentRequest = 'payment-request:delete';
    public const ViewPaymentRequest = 'payment-request:view';

    public const CreateOwnPaymentRequest = 'payment-request:own:create';
    public const UpdateOwnPaymentRequest = 'payment-request:own:update';
    public const DeleteOwnPaymentRequest = 'payment-request:own:delete';
    public const ViewOwnPaymentRequest = 'payment-request:own:view';

    // User abilities
    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';
    public const ViewUser = 'user:view';

    public const UpdateOwnUser = 'user:own:update';
    public const ViewOwnUser = 'user:own:view';

    // Payment Account abilities
    public const CreatePaymentAccount = 'payment-account:create';
    public const UpdatePaymentAccount = 'payment-account:update';
    public const DeletePaymentAccount = 'payment-account:delete';
    public const ViewPaymentAccount = 'payment-account:view';

    public const CreateOwnPaymentAccount = 'payment-account:own:create';
    public const UpdateOwnPaymentAccount = 'payment-account:own:update';
    public const DeleteOwnPaymentAccount = 'payment-account:own:delete';
    public const ViewOwnPaymentAccount = 'payment-account:own:view';

    // Country abilities (read-only for most users)
    public const ViewCountry = 'country:view';
    public const ManageCountry = 'country:manage';

    // Legacy abilities for backward compatibility
    public const CreateKitua = self::CreateOwnPaymentRequest;
    public const UpdateOwnKitua = self::UpdateOwnPaymentRequest;
    public const DeleteOwnKitua = self::DeleteOwnPaymentRequest;
    public const ViewOwnKitua = self::ViewOwnPaymentRequest;

    // Group Payment abilities
    public const CreateGroupPayment = 'group-payment:create';
    public const UpdateOwnGroupPayment = 'group-payment:own:update';
    public const DeleteOwnGroupPayment = 'group-payment:own:delete';
    public const ViewOwnGroupPayment = 'group-payment:own:view';

    // Transaction abilities
    public const ViewOwnTransactions = 'transaction:own:view';
    public const CreateTransaction = 'transaction:create';

    // Device management abilities
    public const ManageOwnDevices = 'device:own:manage';
    public const RevokeOwnDevices = 'device:own:revoke';

    /**
     * Get abilities for a user based on their type and role
     */
    public static function getAbilities(User $user): array
    {
        if ($user->user_type === 'admin') {
            return [
                // Full payment request permissions
                self::CreatePaymentRequest,
                self::UpdatePaymentRequest,
                self::ReplacePaymentRequest,
                self::DeletePaymentRequest,
                self::ViewPaymentRequest,
                
                // Full user permissions
                self::CreateUser,
                self::UpdateUser,
                self::ReplaceUser,
                self::DeleteUser,
                self::ViewUser,

                // Full payment account permissions
                self::CreatePaymentAccount,
                self::UpdatePaymentAccount,
                self::DeletePaymentAccount,
                self::ViewPaymentAccount,

                // Country management
                self::ViewCountry,
                self::ManageCountry,

                // Admin can also manage their own resources
                self::CreateOwnPaymentRequest,
                self::UpdateOwnPaymentRequest,
                self::DeleteOwnPaymentRequest,
                self::ViewOwnPaymentRequest,
                self::UpdateOwnUser,
                self::ViewOwnUser,
                self::CreateOwnPaymentAccount,
                self::UpdateOwnPaymentAccount,
                self::DeleteOwnPaymentAccount,
                self::ViewOwnPaymentAccount,
                
                // Legacy and future abilities
                self::CreateGroupPayment,
                self::UpdateOwnGroupPayment,
                self::DeleteOwnGroupPayment,
                self::ViewOwnGroupPayment,
                self::ViewOwnTransactions,
                self::CreateTransaction,
                self::ManageOwnDevices,
                self::RevokeOwnDevices,
            ];
        } elseif ($user->user_type === 'manager') {
            return [
                // Can manage all payment requests
                self::CreatePaymentRequest,
                self::UpdatePaymentRequest,
                self::ReplacePaymentRequest,
                self::DeletePaymentRequest,
                self::ViewPaymentRequest,

                // Can view all users but not manage them
                self::ViewUser,
                self::UpdateOwnUser,
                self::ViewOwnUser,

                // Can view all payment accounts
                self::ViewPaymentAccount,
                self::CreateOwnPaymentAccount,
                self::UpdateOwnPaymentAccount,
                self::DeleteOwnPaymentAccount,
                self::ViewOwnPaymentAccount,

                // Can view countries
                self::ViewCountry,
                
                // Other abilities
                self::CreateOwnPaymentRequest,
                self::UpdateOwnPaymentRequest,
                self::DeleteOwnPaymentRequest,
                self::ViewOwnPaymentRequest,
                self::CreateGroupPayment,
                self::UpdateOwnGroupPayment,
                self::DeleteOwnGroupPayment,
                self::ViewOwnGroupPayment,
                self::ViewOwnTransactions,
                self::CreateTransaction,
                self::ManageOwnDevices,
                self::RevokeOwnDevices,
            ];
        } else {
            // Regular mobile users - can only manage their own resources
            return [
                self::CreateOwnPaymentRequest,
                self::UpdateOwnPaymentRequest,
                self::DeleteOwnPaymentRequest,
                self::ViewOwnPaymentRequest,

                self::UpdateOwnUser,
                self::ViewOwnUser,

                self::CreateOwnPaymentAccount,
                self::UpdateOwnPaymentAccount,
                self::DeleteOwnPaymentAccount,
                self::ViewOwnPaymentAccount,

                self::ViewCountry,
                
                // Other mobile user abilities
                self::CreateGroupPayment,
                self::UpdateOwnGroupPayment,
                self::DeleteOwnGroupPayment,
                self::ViewOwnGroupPayment,
                self::ViewOwnTransactions,
                self::CreateTransaction,
                self::ManageOwnDevices,
                self::RevokeOwnDevices,
            ];
        }
    }

    /**
     * Get all available abilities
     */
    public static function getAllAbilities(): array
    {
        return [
            self::CreatePaymentRequest,
            self::UpdatePaymentRequest,
            self::ReplacePaymentRequest,
            self::DeletePaymentRequest,
            self::ViewPaymentRequest,
            self::CreateOwnPaymentRequest,
            self::UpdateOwnPaymentRequest,
            self::DeleteOwnPaymentRequest,
            self::ViewOwnPaymentRequest,
            self::CreateUser,
            self::UpdateUser,
            self::ReplaceUser,
            self::DeleteUser,
            self::ViewUser,
            self::UpdateOwnUser,
            self::ViewOwnUser,
            self::CreatePaymentAccount,
            self::UpdatePaymentAccount,
            self::DeletePaymentAccount,
            self::ViewPaymentAccount,
            self::CreateOwnPaymentAccount,
            self::UpdateOwnPaymentAccount,
            self::DeleteOwnPaymentAccount,
            self::ViewOwnPaymentAccount,
            self::ViewCountry,
            self::ManageCountry,
            self::CreateGroupPayment,
            self::UpdateOwnGroupPayment,
            self::DeleteOwnGroupPayment,
            self::ViewOwnGroupPayment,
            self::ViewOwnTransactions,
            self::CreateTransaction,
            self::ManageOwnDevices,
            self::RevokeOwnDevices,
        ];
    }
}
