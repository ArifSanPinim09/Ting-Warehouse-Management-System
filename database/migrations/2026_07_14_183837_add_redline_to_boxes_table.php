<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->boolean('is_redline')->default(false)->after('notes');
            $table->string('redline_note', 500)->nullable()->after('is_redline');
        });
    }

    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['is_redline', 'redline_note']);
        });
    }
};
