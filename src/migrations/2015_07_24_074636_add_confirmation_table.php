<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfirmationTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('unconfirmed_emails', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->string('confirmation_token');
            $table->string('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('unconfirmed_emails');
    }
}
