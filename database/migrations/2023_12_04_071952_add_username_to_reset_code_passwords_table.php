<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::table('reset_code_passwords', function (Blueprint $table) {
            $table->string('username'); // Adjust the data type as needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reset_code_passwords', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
