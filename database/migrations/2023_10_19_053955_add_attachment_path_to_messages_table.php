<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachmentPathToMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('attachment_path')->after('content')->nullable(); // Add the new column after 'content' and make it nullable
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('attachment_path');
        });
    }
}


