<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->string('id')->primary()->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('department');
            $table->string('college');
            $table->string('certificate');
            $table->string('general_jurisdiction');
            $table->string('exact_jurisdiction');
            $table->string('picture')->nullable();
            $table->string('current_promotion');
            $table->date('date_current_promotion');
            $table->string('next_promotion')->nullable();
            $table->date('date_next_promotion')->nullable();
            $table->boolean('user');
            $table->boolean('supervisor')->default(false);
            $table->boolean('MainSobriety')->default(false);
            $table->boolean('Sobriety')->default(false);
            $table->boolean('MainQuote')->default(false);
            $table->boolean('Quote')->default(false);
            $table->boolean('Leader')->default(false);
            $table->boolean('Admin')->default(false);
            $table->boolean('first_member')->default(false);
            $table->boolean('second_member')->default(false);
            $table->boolean('third_member')->default(false);
            $table->boolean('forth_member')->default(false);
            $table->boolean('fifth_member')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
