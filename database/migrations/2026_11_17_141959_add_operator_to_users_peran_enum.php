
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE users
                MODIFY peran ENUM('admin', 'operator', 'pengemudi')
                NOT NULL DEFAULT 'pengemudi'
            ");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('peran')
                    ->default('pengemudi')
                    ->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE users
                MODIFY peran ENUM('admin', 'pengemudi')
                NOT NULL DEFAULT 'pengemudi'
            ");
        }
    }
};
