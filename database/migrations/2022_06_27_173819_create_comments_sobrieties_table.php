<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsSobrietiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments_sobrieties', function (Blueprint $table) {
            $table->id();
            $table->string('sender');
            $table->foreign('sender')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name_sender');
            $table->string('comment_content');
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
        Schema::dropIfExists('comments_sobrieties');
    }
}
