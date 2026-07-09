<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add columns required by Revisi §3.3 while preserving existing data.
     * Existing schema uses polymorphic notifiable + JSON data.
     * New schema needs user_id, title, message, is_read columns.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add user_id column (nullable first for data migration)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            // Add title and message columns
            $table->string('title')->nullable()->after('type');
            $table->text('message')->nullable()->after('title');

            // Add is_read boolean column
            $table->boolean('is_read')->default(false)->after('message');

            // Add indexes per Revisi §3.3
            $table->index(['user_id', 'is_read'], 'idx_user_read');
            $table->index('created_at', 'idx_created');
        });

        // Populate user_id from notifiable_id (where notifiable_type is User)
        DB::table('notifications')
            ->where('notifiable_type', 'App\Models\User')
            ->whereNull('user_id')
            ->update(['user_id' => DB::raw('notifiable_id')]);

        // Populate title and message from JSON data field using PHP
        // (Database-agnostic approach for SQLite compatibility)
        DB::table('notifications')
            ->whereNull('title')
            ->whereNotNull('data')
            ->orderBy('id')
            ->each(function ($notification) {
                $data = json_decode($notification->data, true);
                if ($data) {
                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update([
                            'title' => $data['title'] ?? $notification->type,
                            'message' => $data['message'] ?? '',
                        ]);
                }
            });

        // Populate is_read from read_at
        DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Set default type where null
        DB::table('notifications')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => 'info']);

        // Now make user_id NOT NULL and add foreign key
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('idx_user_read');
            $table->dropIndex('idx_created');
            $table->dropColumn(['user_id', 'title', 'message', 'is_read']);
        });
    }
};
