<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTwoNotificationCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_two_notification_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('paper_id');
            $table->foreign('paper_id')->references('id')->on('table_two_posts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('user_id_for_paper');
            $table->foreign('user_id_for_paper')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('user');
            $table->boolean('supervisor');
            $table->boolean('first_member');
            $table->boolean('second_member');
            $table->boolean('third_member');
            $table->boolean('forth_member');
            $table->boolean('fifth_member');
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
        Schema::dropIfExists('table_two_notification_comments');
    }
}
