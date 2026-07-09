<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

/**
 * Notification Service — In-app notification creator.
 *
 * Creates notification records in the `notifications` table for all
 * business events. Each method accepts the relevant model(s) and
 * builds a standardised data payload.
 *
 * PRD §1.9 / §4.2 / §8.4 / §8.11 / §8.15: Notifikasi in-app.
 */
class NotificationService
{
    // ─── Notification Type Constants ──────────────────────────────

    const TYPE_CUSTOMER_REGISTER = 'customer_register';
    const TYPE_ACCOUNT_ACTIVATED = 'account_activated';
    const TYPE_BOX_STATUS_CHANGED = 'box_status_changed';
    const TYPE_INVOICE_GENERATED = 'invoice_generated';
    const TYPE_PAYMENT_RECEIVED = 'payment_received';
    const TYPE_PAYMENT_VERIFIED = 'payment_verified';
    const TYPE_PAYMENT_REJECTED = 'payment_rejected';
    const TYPE_CHECKOUT_PROCESSED = 'checkout_processed';
    const TYPE_NEW_COMPLAINT = 'new_complaint';
    const TYPE_COMPLAINT_UPDATED = 'complaint_updated';

    // Revisi §2.11.2: Deadline reminder types
    const TYPE_PAYMENT_REMINDER_H3 = 'payment_reminder_h3';
    const TYPE_PAYMENT_REMINDER_H1 = 'payment_reminder_h1';
    const TYPE_PAYMENT_REMINDER_H0 = 'payment_reminder_h0';
    const TYPE_PAYMENT_OVERDUE_2WEEK = 'payment_overdue_2week';
    const TYPE_STORAGE_EXPIRED = 'storage_expired';
    const TYPE_ITEM_HOLD = 'item_hold';

    /**
     * Get all valid notification types.
     *
     * @return array<string>
     */
    public static function getValidTypes(): array
    {
        return [
            self::TYPE_CUSTOMER_REGISTER,
            self::TYPE_ACCOUNT_ACTIVATED,
            self::TYPE_BOX_STATUS_CHANGED,
            self::TYPE_INVOICE_GENERATED,
            self::TYPE_PAYMENT_RECEIVED,
            self::TYPE_PAYMENT_VERIFIED,
            self::TYPE_PAYMENT_REJECTED,
            self::TYPE_CHECKOUT_PROCESSED,
            self::TYPE_NEW_COMPLAINT,
            self::TYPE_COMPLAINT_UPDATED,
            self::TYPE_PAYMENT_REMINDER_H3,
            self::TYPE_PAYMENT_REMINDER_H1,
            self::TYPE_PAYMENT_REMINDER_H0,
            self::TYPE_PAYMENT_OVERDUE_2WEEK,
            self::TYPE_STORAGE_EXPIRED,
            self::TYPE_ITEM_HOLD,
        ];
    }

    /**
     * Customer registered — notify all admin/owner users.
     *
     * PRD §4.4: "Isi form -> Upload -> Submit -> Validasi -> Simpan -> Notif admin"
     *
     * @param  User  $customer  The newly registered customer
     * @return Notification|null  Null when no active admins exist
     */
    public function customerRegister(User $customer): ?Notification
    {
        return $this->notifyAdmins(
            type: self::TYPE_CUSTOMER_REGISTER,
            data: [
                'title' => 'Customer Baru',
                'message' => "{$customer->name} telah mendaftar dan menunggu aktivasi.",
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'link' => route('admin.dashboard'),
            ]
        );
    }

    /**
     * Account activated — notify the customer.
     *
     * @param  User  $customer  The activated customer
     * @return Notification
     */
    public function accountActivated(User $customer): Notification
    {
        return $this->create(
            notifiable: $customer,
            type: self::TYPE_ACCOUNT_ACTIVATED,
            data: [
                'title' => 'Akun Aktif',
                'message' => 'Akun Anda telah diaktivasi. Silakan login untuk mulai menggunakan layanan.',
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Box status changed — notify the customer who owns the box.
     *
     * @param  \App\Models\Box  $box       The box (must have customer loaded)
     * @param  string           $oldStatus Previous status
     * @param  string           $newStatus New status
     * @return Notification
     */
    public function boxStatusChanged($box, string $oldStatus, string $newStatus): Notification
    {
        return $this->create(
            notifiable: $box->customer,
            type: self::TYPE_BOX_STATUS_CHANGED,
            data: [
                'title' => 'Status Box Berubah',
                'message' => "Box {$box->tracking_number} berubah status dari {$oldStatus} menjadi {$newStatus}.",
                'box_id' => $box->id,
                'tracking_number' => $box->tracking_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Invoice generated — notify the customer.
     *
     * @param  \App\Models\Invoice  $invoice  The generated invoice
     * @return Notification
     */
    public function invoiceGenerated($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_INVOICE_GENERATED,
            data: [
                'title' => 'Invoice Baru',
                'message' => "Invoice {$invoice->invoice_number} telah dibuat. Total: Rp " . number_format($invoice->grand_total, 0, ',', '.'),
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'grand_total' => $invoice->grand_total,
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Payment received (customer uploads proof) — notify admin/owner users.
     *
     * @param  \App\Models\Invoice  $invoice  The invoice with payment
     * @return Notification|null  Null when no active admins exist
     */
    public function paymentReceived($invoice): ?Notification
    {
        return $this->notifyAdmins(
            type: self::TYPE_PAYMENT_RECEIVED,
            data: [
                'title' => 'Pembayaran Masuk',
                'message' => "Pembayaran untuk invoice {$invoice->invoice_number} dari {$invoice->customer->name} menunggu verifikasi.",
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'customer_name' => $invoice->customer->name,
                'link' => route('admin.dashboard'),
            ]
        );
    }

    /**
     * Payment verified — notify the customer.
     *
     * @param  \App\Models\Invoice  $invoice  The verified invoice
     * @return Notification
     */
    public function paymentVerified($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_PAYMENT_VERIFIED,
            data: [
                'title' => 'Pembayaran Terverifikasi',
                'message' => "Pembayaran untuk invoice {$invoice->invoice_number} telah diverifikasi. Anda dapat melakukan checkout.",
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Payment rejected — notify the customer.
     *
     * @param  \App\Models\Invoice  $invoice  The rejected invoice
     * @param  string               $reason   Rejection reason
     * @return Notification
     */
    public function paymentRejected($invoice, string $reason): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_PAYMENT_REJECTED,
            data: [
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran untuk invoice {$invoice->invoice_number} ditolak. Alasan: {$reason}",
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'reason' => $reason,
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Checkout processed — notify the customer.
     *
     * @param  \App\Models\Checkout  $checkout  The processed checkout
     * @return Notification
     */
    public function checkoutProcessed($checkout): Notification
    {
        return $this->create(
            notifiable: $checkout->customer,
            type: self::TYPE_CHECKOUT_PROCESSED,
            data: [
                'title' => 'Checkout Diproses',
                'message' => "Checkout Anda untuk invoice {$checkout->invoice->invoice_number} sedang diproses.",
                'checkout_id' => $checkout->id,
                'invoice_number' => $checkout->invoice->invoice_number,
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * New complaint created — notify admin/owner users.
     *
     * @param  \App\Models\Complain  $complaint  The new complaint
     * @return Notification|null  Null when no active admins exist
     */
    public function newComplaint($complaint): ?Notification
    {
        return $this->notifyAdmins(
            type: self::TYPE_NEW_COMPLAINT,
            data: [
                'title' => 'Komplain Baru',
                'message' => "Komplain baru dari {$complaint->customer->name}: {$complaint->type}.",
                'complaint_id' => $complaint->id,
                'customer_name' => $complaint->customer->name,
                'complaint_type' => $complaint->type,
                'link' => route('admin.dashboard'),
            ]
        );
    }

    /**
     * Complaint status updated — notify the customer.
     *
     * @param  \App\Models\Complain  $complaint  The updated complaint
     * @param  string                $oldStatus  Previous status
     * @param  string                $newStatus  New status
     * @return Notification
     */
    public function complaintUpdated($complaint, string $oldStatus, string $newStatus): Notification
    {
        return $this->create(
            notifiable: $complaint->customer,
            type: self::TYPE_COMPLAINT_UPDATED,
            data: [
                'title' => 'Komplain Diperbarui',
                'message' => "Komplain Anda #{$complaint->id} berubah status dari {$oldStatus} menjadi {$newStatus}.",
                'complaint_id' => $complaint->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'link' => route('dashboard'),
            ]
        );
    }

    // ─── Deadline Reminders (Revisi §2.11.2) ─────────────────────

    /**
     * Payment reminder H-3 — 3 days before deadline.
     *
     * @param  \App\Models\Invoice  $invoice  The invoice approaching deadline
     * @return Notification
     */
    public function paymentReminderH3($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_PAYMENT_REMINDER_H3,
            data: [
                'title' => 'Invoice Jatuh Tempo',
                'message' => "Invoice {$invoice->invoice_number} jatuh tempo dalam 3 hari. Segera lakukan pembayaran.",
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'payment_deadline' => $invoice->payment_deadline->format('Y-m-d'),
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Payment reminder H-1 — 1 day before deadline.
     *
     * @param  \App\Models\Invoice  $invoice  The invoice approaching deadline
     * @return Notification
     */
    public function paymentReminderH1($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_PAYMENT_REMINDER_H1,
            data: [
                'title' => 'Invoice Jatuh Tempo Besok',
                'message' => "Invoice {$invoice->invoice_number} jatuh tempo besok! Segera lakukan pembayaran.",
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'payment_deadline' => $invoice->payment_deadline->format('Y-m-d'),
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Payment reminder H-0 — on deadline day.
     *
     * @param  \App\Models\Invoice  $invoice  The invoice at deadline
     * @return Notification
     */
    public function paymentReminderH0($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_PAYMENT_REMINDER_H0,
            data: [
                'title' => 'Invoice Sudah Jatuh Tempo',
                'message' => "Invoice {$invoice->invoice_number} sudah jatuh tempo! Segera lakukan pembayaran.",
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'payment_deadline' => $invoice->payment_deadline->format('Y-m-d'),
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Payment overdue 2 weeks — warning about hold and lelang.
     *
     * @param  \App\Models\Invoice  $invoice  The overdue invoice
     * @return Notification
     */
    public function paymentOverdue2Week($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_PAYMENT_OVERDUE_2WEEK,
            data: [
                'title' => 'Barang Akan Dilelang',
                'message' => 'Barang akan ditahan WH dan bisa dilelang. Hubungi admin untuk info.',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Storage expired — notification that storage deadline has passed.
     *
     * @param  \App\Models\Invoice  $invoice  The invoice with expired storage
     * @return Notification
     */
    public function storageExpired($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_STORAGE_EXPIRED,
            data: [
                'title' => 'Deadline Nimbun Habis',
                'message' => 'Barang Anda sudah melewati deadline nimbun. Akan ditahan WH.',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'storage_deadline' => $invoice->storage_deadline?->format('Y-m-d'),
                'link' => route('dashboard'),
            ]
        );
    }

    /**
     * Item hold — notification that items have been held.
     *
     * @param  \App\Models\Invoice  $invoice  The invoice with held items
     * @return Notification
     */
    public function itemHold($invoice): Notification
    {
        return $this->create(
            notifiable: $invoice->customer,
            type: self::TYPE_ITEM_HOLD,
            data: [
                'title' => 'Barang Ditahan',
                'message' => 'Barang Anda ditahan WH. Hubungi admin.',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'link' => route('dashboard'),
            ]
        );
    }

    // ─── Private Helpers ──────────────────────────────────────────

    /**
     * Create a single notification.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $notifiable  The recipient
     * @param  string                                $type        Notification type
     * @param  array<string, mixed>                  $data        Notification payload
     * @return Notification
     */
    private function create($notifiable, string $type, array $data): Notification
    {
        return Notification::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
            'type' => $type,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to all admin and owner users.
     *
     * @param  string               $type  Notification type
     * @param  array<string, mixed> $data  Notification payload
     * @return Notification|null  Null when no active admins exist
     */
    private function notifyAdmins(string $type, array $data): ?Notification
    {
        $admins = User::whereIn('role', ['admin', 'owner'])
            ->where('status', User::STATUS_ACTIVE)
            ->get();

        $lastNotification = null;

        foreach ($admins as $admin) {
            $lastNotification = $this->create(
                notifiable: $admin,
                type: $type,
                data: $data
            );
        }

        return $lastNotification;
    }
}
