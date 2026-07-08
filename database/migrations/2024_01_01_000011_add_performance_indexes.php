<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add performance indexes for realistic data scale.
 *
 * §5.2: DB query < 100ms. These indexes cover the most common
 * WHERE, ORDER BY, and JOIN patterns in Livewire components.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── Users table ─────────────────────────────────────────
        // Already has: role(index), status(index)
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'users_role_status_idx'); // Admin dashboard stats
        });

        // ─── Boxes table ─────────────────────────────────────────
        // Already has: type(index), status(index), customer_id(index), method(index)
        Schema::table('boxes', function (Blueprint $table) {
            $table->index(['customer_id', 'status'], 'boxes_customer_status_idx'); // Customer dashboard
            $table->index(['status', 'created_at'], 'boxes_status_created_idx'); // Admin dashboard stats
        });

        // ─── Invoices table ──────────────────────────────────────
        // Already has: [box_id, customer_id](index), status(index)
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['customer_id', 'status'], 'invoices_customer_status_idx'); // Customer dashboard
            $table->index(['status', 'grand_total'], 'invoices_status_total_idx'); // Owner finance stats
            $table->index(['status', 'updated_at'], 'invoices_status_updated_idx'); // Owner revenue by month
            $table->index('invoice_number', 'invoices_number_idx'); // Search by invoice number
        });

        // ─── Items table ─────────────────────────────────────────
        // Already has: [box_id, customer_id](index), resi_number(index)
        Schema::table('items', function (Blueprint $table) {
            $table->index(['customer_id', 'created_at'], 'items_customer_created_idx'); // Customer dashboard goods count
        });

        // ─── Checkouts table ─────────────────────────────────────
        // Already has: [invoice_id, customer_id](index), status(index)
        Schema::table('checkouts', function (Blueprint $table) {
            $table->index(['customer_id', 'status'], 'checkouts_customer_status_idx'); // Customer dashboard
        });

        // ─── Complains table ─────────────────────────────────────
        // Already has: [customer_id, box_id](index), status(index)
        Schema::table('complains', function (Blueprint $table) {
            $table->index(['customer_id', 'status'], 'complains_customer_status_idx'); // Customer dashboard
        });

        // ─── Notifications table ─────────────────────────────────
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifs_type_id_read_idx'); // Bell component
        });

        // ─── Activity logs table ─────────────────────────────────
        // Already has: event(index)
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['subject_type', 'subject_id'], 'activity_subject_idx'); // Audit log detail
            $table->index(['user_id', 'created_at'], 'activity_user_created_idx'); // Admin activity history
        });

        // ─── Settings table ──────────────────────────────────────
        // Already has: group(index)
        Schema::table('settings', function (Blueprint $table) {
            $table->index('key', 'settings_key_idx'); // getValue lookups
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_status_idx');
        });
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropIndex('boxes_customer_status_idx');
            $table->dropIndex('boxes_status_created_idx');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_customer_status_idx');
            $table->dropIndex('invoices_status_total_idx');
            $table->dropIndex('invoices_status_updated_idx');
            $table->dropIndex('invoices_number_idx');
        });
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_customer_created_idx');
        });
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropIndex('checkouts_customer_status_idx');
        });
        Schema::table('complains', function (Blueprint $table) {
            $table->dropIndex('complains_customer_status_idx');
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifs_type_id_read_idx');
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_subject_idx');
            $table->dropIndex('activity_user_created_idx');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_key_idx');
        });
    }
};
