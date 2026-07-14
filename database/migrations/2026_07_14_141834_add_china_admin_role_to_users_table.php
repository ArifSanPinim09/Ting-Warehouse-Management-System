<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // MySQL: use MODIFY to extend enum
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'admin', 'customer', 'china_admin') DEFAULT 'customer'");
        }
        // SQLite: role column is already VARCHAR/TEXT — no enum constraint to modify
        // The application-level validation in User model handles allowed values.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'admin', 'customer') DEFAULT 'customer'");
        }
    }
};
