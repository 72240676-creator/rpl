<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chargers', function (Blueprint $table) {
            $table->boolean('needs_inspection')->default(false)->index();
            $table->text('inspection_note')->nullable();
            $table->timestamp('inspection_reported_at')->nullable();
            $table->unsignedBigInteger('inspection_reported_by')->nullable();
            $table->foreign('inspection_reported_by')
                ->references('id_user')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chargers', function (Blueprint $table) {
            $table->dropForeign(['inspection_reported_by']);
            $table->dropIndex(['needs_inspection']);
            $table->dropColumn([
                'needs_inspection',
                'inspection_note',
                'inspection_reported_at',
                'inspection_reported_by',
            ]);
        });
    }
};
