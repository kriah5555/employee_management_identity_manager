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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username')->unique();
                $table->string('password');
                $table->string('email')->nullable();
                $table->boolean('status')->default(true);
                $table->unsignedBigInteger('created_by');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('updated_by');
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
