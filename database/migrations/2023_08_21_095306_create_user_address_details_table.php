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
        Schema::create('user_address_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('street');
            $table->string('house_number');
            $table->string('city');
            $table->string('postal_code');
            $table->integer('country_id')->references('id')->on('countries');
            $table->string('lattitude')->nullable();
            $table->string('longtitude')->nullable();
            $table->date('from');
            $table->date('to')->nullable();
            $table->timestamps();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_address_details');
    }
};
