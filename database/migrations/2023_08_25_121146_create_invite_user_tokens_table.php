<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invite_user_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('mail');
            $table->string('token');
            $table->string('expire_at');
            $table->string('status')->default(1);
            $table->string('invite_role')->nullable();
            $table->foreignId('invite_by')->references('id')->on('users')->onDelete('cascade');
            $table->integer('company_id')->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite_user_tokens');
    }
};