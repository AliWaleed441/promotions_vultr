<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOnePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_one_posts', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('number_of_tables_id');
            //$table->foreign('number_of_tables_id')->references('number_of_tables_id')->on('number_of_tables')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('search_title');
            $table->string('publisher');
            $table->string('is_impact');
            $table->string('is_single');
            $table->bigInteger('scores');
            $table->bigInteger('year');
            $table->string('attachment')->nullable();
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
        Schema::dropIfExists('table_one_posts');
    }
}
