
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_pengguna', function (Blueprint $table) {
            $table->boolean('diteruskan_ke_admin')
                ->default(false);

            $table->unsignedBigInteger('operator_id')
                ->nullable();

            $table->foreign('operator_id')
                ->references('id_user')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_pengguna', function (Blueprint $table) {
            $table->dropForeign(['operator_id']);
            $table->dropColumn([
                'diteruskan_ke_admin',
                'operator_id',
            ]);
        });
    }
};
