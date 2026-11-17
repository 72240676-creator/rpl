<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY peran ENUM('admin', 'operator', 'pengemudi')
            NOT NULL DEFAULT 'pengemudi'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY peran ENUM('admin', 'pengemudi')
            NOT NULL DEFAULT 'pengemudi'
        ");
    }
};