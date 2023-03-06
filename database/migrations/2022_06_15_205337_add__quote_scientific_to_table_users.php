<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuoteScientificToTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_files', function (Blueprint $table) {
            $table->integer('QuoteSc')->default(1);
            $table->date('QuoteScReq')->nullable();
            $table->date('QuoteScSendLeader')->nullable();
            $table->date('QuoteScSendAtt')->nullable();
            $table->string('QuoteScAttachment')->nullable();
            $table->date('QuoteScAccRejLeader')->nullable();
            $table->date('QuoteScAccRejSub')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->String('MainQuoteSc')->nullable();
            $table->String('QuoteSc')->nullable();
        });
        Schema::table('table_two_posts', function (Blueprint $table) {
            $table->string('is_single')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_files', function (Blueprint $table) {
            $table->dropColumn('QuoteSc');
            $table->dropColumn('QuoteScReq');
            $table->dropColumn('QuoteScSendLeader');
            $table->dropColumn('QuoteScSendAtt');
            $table->dropColumn('QuoteScAttachment');
            $table->dropColumn('QuoteScAccRejLeader');
            $table->dropColumn('QuoteScAccRejSub');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('MainQuoteSc');
            $table->dropColumn('QuoteSc');

        });
    }
}
