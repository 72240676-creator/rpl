<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('operator_id');
            
            $table->foreign('operator_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            $table->string('name');
            $table->text('address');

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->string('operational_hours')->nullable();

            $table->json('facilities')->nullable();

            $table->string('photo_url')->nullable();

            $table->enum('status', [
                'active',
                'closed_temporary',
                'full',
                'maintenance'
            ])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};