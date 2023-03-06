<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_files', function (Blueprint $table) {
            //$table->id();
            $table->string('user_id');
            $table->primary('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            //اذا 1 لم يطلب بعد -- 2 طلب من قبل المستخدم -- 3 ارسل رئيس القسم -- 4 ارسلت اللجنة -- 5 وافق رئيس القسم
            //اذا 6 وافق المشرف -- 7 لم يوافق رئيس القسم -- 8 لم يوافق المشرف -- 9 وافق رئيس القسم ولم يوافق المشرف -- 10 وافق المشرف ولم يوافق رئيس القسم
            // اذا 12 وافق كليهما -- 0  رفضت
            $table->integer('Sobriety')->default(1);//حالة الرصانة
            $table->date('SobrietyReq')->nullable();//تاريخ طلب الرصانة من المستخدم
            $table->date('SobrietySendLeader')->nullable();//ارسال الرصانة من رئيس القسم الى اللجنة
            $table->date('SobrietySendAtt')->nullable();//تاريخ ارسال ملف الرصانة من قبل اللجنة
            $table->string('SobrietyAttachment')->nullable();//ملف الرصانة
            $table->date('SobrietyAccRejLeader')->nullable();//تاريخ قبول او رفض الرصانة من رئيس القسم
            $table->date('SobrietyAccRejSub')->nullable();//تاريخ موافقة او رفض المشرف

            $table->integer('Quote')->default(1);
            $table->date('QuoteReq')->nullable();
            $table->date('QuoteSendLeader')->nullable();
            $table->date('QuoteSendAtt')->nullable();
            $table->string('QuoteAttachment')->nullable();
            $table->date('QuoteAccRejLeader')->nullable();
            $table->date('QuoteAccRejSub')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_files');
    }
}
