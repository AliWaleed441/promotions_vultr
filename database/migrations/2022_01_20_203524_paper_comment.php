<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaperComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('paper_id');
            $table->foreign('paper_id')->references('id')->on('table_one_posts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('sender');
            $table->foreign('sender')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('paper_comment');
    }
}
