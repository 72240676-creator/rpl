<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('transactions', 'session_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('session_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('transactions', 'session_id')) {
            return;
        }

        if (DB::table('transactions')->whereNull('session_id')->exists()) {
            throw new RuntimeException(
                'Cannot make transactions.session_id required while transactions without a charging session exist.'
            );
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable(false)->change();
        });
    }
};
