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
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique()->nullable();
                $table->integer('category_id')->nullable();
                $table->boolean('status')->default(true);
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                $table->integer('created_by')->nullable(true);
                $table->integer('updated_by')->nullable(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
