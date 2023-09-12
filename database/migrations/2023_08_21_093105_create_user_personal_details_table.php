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
        if (!Schema::hasTable('user_personal_details')) {
            Schema::create('user_personal_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->references('id')->on('users');
                $table->string('marital_status_id')->references('id')->on('marital_statuses');
                $table->integer('dependent_spouse_id')->default(0);
                $table->integer('dependent_children')->default(0);
                $table->string('personal_mail')->nullable();
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
        Schema::dropIfExists('user_personal_details');
    }
};
