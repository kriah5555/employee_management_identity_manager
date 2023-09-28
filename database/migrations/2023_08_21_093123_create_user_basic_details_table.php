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
        if (!Schema::hasTable('user_basic_details')) {
            Schema::create('user_basic_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->references('id')->on('users');
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('mobile')->nullable();
                $table->string('rsz_number');
                $table->date('birth_date');
                $table->string('birth_place');
                $table->string('bank_account');
                $table->foreignId('gender_id')->references('id')->on('genders');
                $table->foreignId('nationality_id')->references('id')->on('country_nationalities');
                $table->foreignId('language_id')->references('id')->on('languages');
                $table->boolean('status')->default(true);
                $table->integer('created_by')->default(0);
                $table->integer('updated_by')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_basic_details');
    }
};
