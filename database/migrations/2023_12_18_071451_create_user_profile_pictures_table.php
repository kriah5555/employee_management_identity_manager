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
        Schema::create('user_profile_pictures', function (Blueprint $table) {
            $table->id(); // Auto-incremental primary key
            $table->unsignedBigInteger('user_id'); // Foreign key to link with the users table
            $table->string('image_path')->nullable(); // Path or URL to the user's profile picture
            $table->string('image_name')->nullable(); // Path or URL to the user's profile picture
            $table->timestamps(); // Created_at and updated_at columns
            $table->softDeletes(); // Adds the deleted_at column

            // Foreign key constraint to link with the users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_pictures');
    }
};



