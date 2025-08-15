<?php

namespace App\Permissions\V1;

use App\Models\User;

final class Abilities {
    // Payment Account abilities
    public const CreatePaymentAccount = 'payment_account:create';
    public const UpdatePaymentAccount = 'payment_account:update';
    public const DeletePaymentAccount = 'payment_account:delete';
    public const ViewPaymentAccount = 'payment_account:view';

    // Kitua (Payment Request) abilities
    public const CreateKitua = 'kitua:create';
    public const UpdateOwnKitua = 'kitua:own:update';
    public const DeleteOwnKitua = 'kitua:own:delete';
    public const ViewOwnKitua = 'kitua:own:view';

    // Group Payment abilities
    public const CreateGroupPayment = 'group_payment:create';
    public const UpdateOwnGroupPayment = 'group_payment:own:update';
    public const DeleteOwnGroupPayment = 'group_payment:own:delete';
    public const ViewOwnGroupPayment = 'group_payment:own:view';

    // Transaction abilities
    public const ViewOwnTransactions = 'transaction:own:view';
    public const CreateTransaction = 'transaction:create';

    // Device management abilities
    public const ManageOwnDevices = 'device:own:manage';
    public const RevokeOwnDevices = 'device:own:revoke';

    // Admin abilities
    public const ViewAllUsers = 'user:view:all';
    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const DeleteUser = 'user:delete';
    public const ViewAllTransactions = 'transaction:view:all';
    public const ViewAllKitua = 'kitua:view:all';
    public const ViewAllGroupPayments = 'group_payment:view:all';
    public const ManageSystem = 'system:manage';

    public static function getAbilities(User $user): array 
    {
        if ($user->isAdminUser()) {
            return [
                // Admin has all abilities
                self::CreatePaymentAccount,
                self::UpdatePaymentAccount,
                self::DeletePaymentAccount,
                self::ViewPaymentAccount,
                self::CreateKitua,
                self::UpdateOwnKitua,
                self::DeleteOwnKitua,
                self::ViewOwnKitua,
                self::CreateGroupPayment,
                self::UpdateOwnGroupPayment,
                self::DeleteOwnGroupPayment,
                self::ViewOwnGroupPayment,
                self::ViewOwnTransactions,
                self::CreateTransaction,
                self::ManageOwnDevices,
                self::RevokeOwnDevices,
                // Admin-specific abilities
                self::ViewAllUsers,
                self::CreateUser,
                self::UpdateUser,
                self::DeleteUser,
                self::ViewAllTransactions,
                self::ViewAllKitua,
                self::ViewAllGroupPayments,
                self::ManageSystem,
            ];
        } else {
            // Mobile users have limited abilities
            return [
                self::CreatePaymentAccount,
                self::UpdatePaymentAccount,
                self::DeletePaymentAccount,
                self::ViewPaymentAccount,
                self::CreateKitua,
                self::UpdateOwnKitua,
                self::DeleteOwnKitua,
                self::ViewOwnKitua,
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
}
