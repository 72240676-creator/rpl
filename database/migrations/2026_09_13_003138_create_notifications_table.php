<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')
                      ->references('id_user') // Sesuaikan jika primary key tabel users adalah 'id' atau 'id_user'
                      ->on('users')
                      ->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
                    $table->string('title')->after('user_id');
                    $table->text('message')->after('title');
                    $table->boolean('is_read')->default(false)->after('message');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};