<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sprint 2 — Admin China: 3 tabel baru
     * 
     * 1. shipping_material_fees — Form: category, name, biaya (yuan), status PAID/UNPAID
     * 2. goods_weight_fees — Admin China input (yuan), Owner lihat (rupiah via kurs)
     * 3. cargo_destinations — NCES / NCSS / NC / TRI (pilihan alamat saat SEND ke cargo)
     */
    public function up(): void
    {
        // 1. Shipping & Material Fees
        Schema::create('shipping_material_fees', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50); // shipping, material, operational, other
            $table->string('name', 255);
            $table->decimal('biaya_yuan', 12, 2)->default(0);
            $table->enum('status', ['PAID', 'UNPAID'])->default('UNPAID');
            $table->foreignId('box_id')->nullable()->constrained('boxes')->nullOnDelete();
            $table->string('notes', 500)->nullable();
            $table->foreignId('input_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['category', 'status']);
            $table->index('box_id');
        });

        // 2. Goods Weight Fees — Admin China isi berat (yuan), Owner lihat (rupiah)
        Schema::create('goods_weight_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_id')->nullable()->constrained('boxes')->nullOnDelete();
            $table->string('huruf_box', 10)->nullable();
            $table->decimal('berat_kg', 10, 2);
            $table->decimal('biaya_yuan', 12, 2)->default(0);
            $table->enum('status', ['PAID', 'UNPAID'])->default('UNPAID');
            $table->string('notes', 500)->nullable();
            $table->foreignId('input_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['box_id', 'status']);
        });

        // 3. Cargo Destinations — NCES / NCSS / NC / TRI
        Schema::create('cargo_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // NCES, NCSS, NC, TRI
            $table->string('name', 255);
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Add cargo_destination + cargo_tracking_number to boxes
        Schema::table('boxes', function (Blueprint $table) {
            $table->string('cargo_destination', 10)->nullable()->after('tracking_number');
            $table->string('cargo_tracking_number', 100)->nullable()->after('cargo_destination');
            $table->string('cargo_photo', 255)->nullable()->after('cargo_tracking_number');
        });

        // Seed default destinations
        DB::table('cargo_destinations')->insert([
            ['code' => 'NCES', 'name' => 'NCES — East Storage', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'NCSS', 'name' => 'NCSS — South Storage', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'NC',   'name' => 'NC — North Cargo',     'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'TRI',  'name' => 'TRI — Tri Cargo',      'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['cargo_destination', 'cargo_tracking_number', 'cargo_photo']);
        });

        Schema::dropIfExists('cargo_destinations');
        Schema::dropIfExists('goods_weight_fees');
        Schema::dropIfExists('shipping_material_fees');
    }
};
