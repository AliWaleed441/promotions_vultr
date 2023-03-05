<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuoteScMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_sc__members', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('MainQuoteSc');
            $table->string('current_promotion1');
            $table->string('name1');
            $table->string('SecondQuoteSc');
            $table->string('current_promotion2');
            $table->string('name2');
            $table->string('thirdQuoteSc');
            $table->string('current_promotion3');
            $table->string('name3');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_sc__members');
    }
}
