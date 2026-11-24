
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('peran')
                ->default('pengemudi')
                ->change();
        });
    }

    public function down(): void
    {
        // Tidak menghapus kolom peran agar data pengguna tetap aman.
    }
};
