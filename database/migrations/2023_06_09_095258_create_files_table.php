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
        if (!Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->increments('fid');
                $table->string('file_path', 255)->nullable();
                $table->string('file_mime', 255)->nullable();
                $table->string('file_size')->nullable();
                $table->string('file_name', 255)->nullable();
                $table->string('file_status')->nullable();
                $table->string('file_location')->nullable();
                $table->string('file_format')->nullable();
                $table->index(['file_path', 'file_name', 'fid']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
