<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'payment_url')) {
                $table->text('payment_url')->nullable();
            }

            if (!Schema::hasColumn('transactions', 'provider_transaction_id')) {
                $table->string('provider_transaction_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'provider_transaction_id')) {
                $table->dropIndex(['provider_transaction_id']);
                $table->dropColumn('provider_transaction_id');
            }

            if (Schema::hasColumn('transactions', 'payment_url')) {
                $table->dropColumn('payment_url');
            }
        });
    }
};
