<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sprint 3 — Customer: Blacklist + TnC + Ekspedisi + Payment Timeout
     */
    public function up(): void
    {
        // 1. Add blacklist + TnC to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_blacklisted')->default(false)->after('status');
            $table->text('blacklist_reason')->nullable()->after('is_blacklisted');
            $table->timestamp('blacklisted_at')->nullable()->after('blacklist_reason');
            $table->boolean('tnc_accepted')->default(false)->after('blacklisted_at');
            $table->timestamp('tnc_accepted_at')->nullable()->after('tnc_accepted');
        });

        // 2. Ekspedisi (courier) table — for checkout pilih ekspedisi
        Schema::create('ekspedisis', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // JNE, JNT, SiCepat, etc
            $table->string('code', 10)->unique();
            $table->string('logo', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Add ekspedisi_id + payment_timeout to checkouts
        Schema::table('checkouts', function (Blueprint $table) {
            $table->foreignId('ekspedisi_id')->nullable()->constrained('ekspedisis')->nullOnDelete();
            $table->decimal('ongkir', 12, 2)->default(0)->after('ekspedisi_id');
            $table->timestamp('payment_timeout_at')->nullable()->after('status');
        });

        // 4. Seed default ekspedisi
        DB::table('ekspedisis')->insert([
            ['name' => 'JNE', 'code' => 'JNE', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'J&T Express', 'code' => 'JNT', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SiCepat', 'code' => 'SCP', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Anteraja', 'code' => 'ANT', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ninja Express', 'code' => 'NJV', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropForeign(['ekspedisi_id']);
            $table->dropColumn(['ekspedisi_id', 'ongkir', 'payment_timeout_at']);
        });

        Schema::dropIfExists('ekspedisis');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_blacklisted', 'blacklist_reason', 'blacklisted_at',
                'tnc_accepted', 'tnc_accepted_at'
            ]);
        });
    }
};
