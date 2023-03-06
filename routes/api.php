<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::get('/user', function (Request $request) {
    return $request->user();
    });
    Route::get('/logout', [\App\Http\Controllers\AuthController::class,'logout']);
    Route::put('/submitToPromotion', [\App\Http\Controllers\AuthController::class,'submitToPromotion']);
    Route::put('/changepicture', [\App\Http\Controllers\AuthController::class,'changepicture']);
    Route::get('/getAllUser', [\App\Http\Controllers\AuthController::class,'getAllUser']);
    Route::put('/editPassword', [\App\Http\Controllers\AuthController::class,'editPassword']);
    Route::get('/getInformationUserForSup/{id}', [\App\Http\Controllers\AuthController::class,'getInformationUser']);


    Route::post('/addCommentTableOne',[\App\Http\Controllers\PaperCommentController::class,'store']);
    Route::post('/showCommentTableOne',[\App\Http\Controllers\PaperCommentController::class,'show']);

    Route::post('/addCommentTableTwo',[\App\Http\Controllers\TableTwoCommentController::class,'store']);
    Route::post('/showCommentTableTwo',[\App\Http\Controllers\TableTwoCommentController::class,'show']);

    Route::group(['middleware'=>'isSupervisor'],function(){
        Route::get('/getSobrietyUserSub', [\App\Http\Controllers\Admin::class,'getSobrietyUser']);
        Route::get('/getQuoteUserSub', [\App\Http\Controllers\Admin::class,'getQuoteUser']);
        Route::get('/getQuoteScUserSub', [\App\Http\Controllers\Admin::class,'getQuoteScUser']);
        Route::get('/showPackage',[\App\Http\Controllers\PackageFileController::class,'showForSubervisor']);
    });
    Route::get('/showPackageForUser',[\App\Http\Controllers\PackageFileController::class,'showPackageForUser']);

    Route::post('/addPostTableOne',[\App\Http\Controllers\TableOnePostController::class,'store']);
    Route::get('/showForUserPostTableOne',[\App\Http\Controllers\TableOnePostController::class,'showForUser']);
    Route::post('/showForSupervisorPostTableOne',[\App\Http\Controllers\TableOnePostController::class,'showForSupervisor']);
    Route::post('/getFileTableOne',[\App\Http\Controllers\TableOnePostController::class,'getfile']);
    Route::put('/addFileTableOne',[\App\Http\Controllers\TableOnePostController::class,'addfile']);
    Route::put('/changePointsTableOne',[\App\Http\Controllers\TableOnePostController::class,'changepoints']);
    Route::delete('/deletePostTableOne/{id}',[\App\Http\Controllers\TableOnePostController::class,'deletePost']);
    Route::delete('/deleteFileTableOne',[\App\Http\Controllers\TableOnePostController::class,'deleteFile']);

    Route::post('/addPostTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'store']);
    Route::get('/showForUserPostTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'showForUser']);
    Route::post('/showForSupervisorPostTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'showForSupervisor']);
    Route::post('/getFileTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'getfile']);
    Route::put('/addFileTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'addfile']);
    Route::put('/changePointsTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'changePoints']);
    Route::put('/reverseSearchTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'ReverseSearch']);
    Route::delete('/deletePostTableTwo/{id}',[\App\Http\Controllers\TableTwoPostsController::class,'deletePost']);
    Route::delete('/deleteFileTableTwo',[\App\Http\Controllers\TableTwoPostsController::class,'deleteFile']);

    Route::get('/NotificationCommentforuser',[\App\Http\Controllers\NotificationCommentController::class,'showforuser']);

    Route::group(['middleware'=>'isLeader'],function(){
        //رصانة -- رئيس
        Route::post('/SendToSobrietyMemberLeader',[\App\Http\Controllers\StepPromotion\StepSobriety\StepLeader::class,'SendToSobrietyMember']);
        Route::get('/getUserWaitSendSobriety',[\App\Http\Controllers\StepPromotion\StepSobriety\StepLeader::class,'getUserWaitSendSobriety']);
        Route::post('/showForSendSobrietyPostTables',[\App\Http\Controllers\StepPromotion\StepSobriety\StepLeader::class,'showForSendSobrietyPostTables']);
        Route::get('/getUserWaitAcceptSobrietyLeader',[\App\Http\Controllers\StepPromotion\StepSobriety\StepLeader::class,'getUserWaitAcceptSobriety']);
        Route::post('/AcceptSobrietyLeader',[\App\Http\Controllers\StepPromotion\StepSobriety\StepLeader::class,'AcceptSobriety']);
        Route::post('/RejectSobrietyLeader',[\App\Http\Controllers\StepPromotion\StepSobriety\StepLeader::class,'RejectSobriety']);

        //استلال الكتروني -- رئيس
        Route::post('/SendToQuoteMemberLeader',[\App\Http\Controllers\StepPromotion\StepQuote\StepLeader::class,'SendToQuoteMember']);
        Route::get('/getUserWaitSendQuote',[\App\Http\Controllers\StepPromotion\StepQuote\StepLeader::class,'getUserWaitSendQuote']);
        Route::post('/showForSendQuotePost',[\App\Http\Controllers\StepPromotion\StepQuote\StepLeader::class,'showForSendQuotePost']);
        Route::get('/getUserWaitAcceptQuoteLeader',[\App\Http\Controllers\StepPromotion\StepQuote\StepLeader::class,'getUserWaitAcceptQuote']);
        Route::post('/AcceptQuoteLeader',[\App\Http\Controllers\StepPromotion\StepQuote\StepLeader::class,'AcceptQuote']);
        Route::post('/RejectQuoteLeader',[\App\Http\Controllers\StepPromotion\StepQuote\StepLeader::class,'RejectQuote']);

        //استلال علمي -- رئيس
        Route::put('/formationQuoteSc',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepLeader::class,'formationQuoteSc']);
        Route::get('/getUserWaitSendQuoteSc',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepLeader::class,'getUserWaitSendQuoteSc']);
        Route::post('/showForSendQuoteScPost',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepLeader::class,'showForSendQuoteScPost']);
        Route::get('/getUserWaitAcceptQuoteScLeader',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepLeader::class,'getUserWaitAcceptQuoteSc']);
        Route::post('/AcceptQuoteScLeader',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepLeader::class,'AcceptQuoteSc']);
        Route::post('/RejectQuoteScLeader',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepLeader::class,'RejectQuoteSc']);

        Route::get('/getSobrietyUserForLeader', [\App\Http\Controllers\Admin::class,'getSobrietyUser']);
        Route::get('/getQuoteUserForLeader', [\App\Http\Controllers\Admin::class,'getQuoteUser']);
        Route::put('/changeSobrietyUserForLeader', [\App\Http\Controllers\Admin::class,'changeSobrietyUser']);
        Route::put('/changeQuoteUserForLeader', [\App\Http\Controllers\Admin::class,'changeQuoteUser']);
    });

    Route::group(['middleware'=>'isSupervisor'],function(){
        //رصانة -- مشرف
        Route::get('/getUserWaitAcceptSobrietySupervisor',[\App\Http\Controllers\StepPromotion\StepSobriety\StepSupervisor::class,'getUserWaitAcceptSobriety']);
        Route::post('/AcceptSobrietySupervisor',[\App\Http\Controllers\StepPromotion\StepSobriety\StepSupervisor::class,'AcceptSobriety']);
        Route::post('/RejectSobrietySupervisor',[\App\Http\Controllers\StepPromotion\StepSobriety\StepSupervisor::class,'RejectSobriety']);

        //استلال الكتروني -- مشرف
        Route::get('/getUserWaitAcceptQuoteSupervisor',[\App\Http\Controllers\StepPromotion\StepQuote\StepSupervisor::class,'getUserWaitAcceptQuote']);
        Route::post('/AcceptQuoteSupervisor',[\App\Http\Controllers\StepPromotion\StepQuote\StepSupervisor::class,'AcceptQuote']);
        Route::post('/RejectQuoteSupervisor',[\App\Http\Controllers\StepPromotion\StepQuote\StepSupervisor::class,'RejectQuote']);

            //استلال علمي -- مشرف
        Route::get('/getUserWaitAcceptQuoteScSupervisor',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepSupervisor::class,'getUserWaitAcceptQuoteSc']);
        Route::post('/AcceptQuoteScSupervisor',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepSupervisor::class,'AcceptQuoteSc']);
        Route::post('/RejectQuoteScSupervisor',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepSupervisor::class,'RejectQuoteSc']);

        Route::post('/storeCommentToSobriety',[\App\Http\Controllers\CommentsSobrietyController::class,'store']);
        Route::get('/getCommentToSobriety',[\App\Http\Controllers\CommentsSobrietyController::class,'show']);
        Route::post('/storeCommentToQuote',[\App\Http\Controllers\CommentsQuoteController::class,'store']);
        Route::get('/getCommentToQuote',[\App\Http\Controllers\CommentsQuoteController::class,'show']);
        Route::post('/storeCommentToQuoteSc',[\App\Http\Controllers\CommentsQuoteScController::class,'store']);
        Route::get('/getCommentToQuoteSc/{quoteId}',[\App\Http\Controllers\CommentsQuoteScController::class,'showForSupervisor']);

    });

    Route::group(['middleware'=>'isQuote'],function(){
        //استلال الكتروني -- اعضاء الاستلال
        Route::get('/getUserWaitQuote',[\App\Http\Controllers\StepPromotion\StepQuote\StepQuoteMember::class,'getUserWaitQuote']);
        Route::post('/showForQuotePostTables',[\App\Http\Controllers\StepPromotion\StepQuote\StepQuoteMember::class,'showForQuotePostTables']);

        Route::group(['middleware'=>'isMainQuote'],function(){
            //استلال الكتروني --رئيس اعضاء الاستلال
            Route::post('/addFileQuoteForTables',[\App\Http\Controllers\StepPromotion\StepQuote\StepQuoteMember::class,'addFileQuoteForTables']);
            Route::post('/storeCommentQuote',[\App\Http\Controllers\CommentsQuoteController::class,'store']);
        });
        Route::get('/getCommentQuote',[\App\Http\Controllers\CommentsQuoteController::class,'show']);
    });


        //استلال علمي -- اعضاء الاستلال
        Route::get('/getUserWaitQuoteSc',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepQuoteScientificMember::class,'getUserWaitQuoteSc']);
        Route::post('/showForQuoteScPostTables',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepQuoteScientificMember::class,'showForQuoteScPostTables']);
        Route::get('/getQuoteScMember/{id}',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepQuoteScientificMember::class,'getQuoteScMember']);

        Route::post('/addFileQuoteScForTables',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepQuoteScientificMember::class,'addFileQuoteScForTables']);

        Route::post('/storeCommentQuoteSc',[\App\Http\Controllers\CommentsQuoteScController::class,'store']);
        Route::get('/getCommentQuoteSc',[\App\Http\Controllers\CommentsQuoteScController::class,'show']);


    Route::group(['middleware'=>'isSobriety'],function(){
            //رصانة -- اعضاء رصانة
        Route::get('/getUserWaitSobriety',[\App\Http\Controllers\StepPromotion\StepSobriety\StepSobrietyMember::class,'getUserWaitSobriety']);
        Route::post('/showForSobrietyPostTables',[\App\Http\Controllers\StepPromotion\StepSobriety\StepSobrietyMember::class,'showForSobrietyPostTables']);
        Route::post('/addFileSobrietyForTables',[\App\Http\Controllers\StepPromotion\StepSobriety\StepSobrietyMember::class,'addFileSobrietyForTables']);

        Route::group(['middleware'=>'isMainSobriety'],function(){
            //استلال الكتروني --رئيس اعضاء الرصانة
            Route::post('/addFileQuoteForTables',[\App\Http\Controllers\StepPromotion\StepQuote\StepQuoteMember::class,'addFileQuoteForTables']);

            Route::post('/storeCommentSobriety',[\App\Http\Controllers\CommentsSobrietyController::class,'store']);
        });
        Route::get('/getCommentSobriety',[\App\Http\Controllers\CommentsSobrietyController::class,'show']);
    });

    //رصانة -- مستخدم
    Route::get('/requestSobrietyTables',[\App\Http\Controllers\StepPromotion\StepSobriety\StepUser::class,'requestSobrietyTables']);

    //استلال الكتروني -- مستخدم
    Route::get('/requestQuoteTables',[\App\Http\Controllers\StepPromotion\StepQuote\StepUser::class,'requestQuoteTables']);

    //استلال علمي -- مستخدم
    Route::get('/requestQuoteScTables',[\App\Http\Controllers\StepPromotion\StepQuoteScientific\StepUser::class,'requestQuoteScTables']);


    Route::group(['middleware'=>'isAdmin'],function(){
        Route::get('/getSobrietyUser', [\App\Http\Controllers\Admin::class,'getSobrietyUser']);
        Route::get('/getQuoteUser', [\App\Http\Controllers\Admin::class,'getQuoteUser']);
        Route::put('/changeSobrietyUser', [\App\Http\Controllers\Admin::class,'changeSobrietyUser']);
        Route::put('/changeQuoteUser', [\App\Http\Controllers\Admin::class,'changeQuoteUser']);
        Route::post('/addMember', [\App\Http\Controllers\Admin::class,'addMember']);
        Route::get('/getInformationUser/{id}', [\App\Http\Controllers\Admin::class,'getInformationUser']);
        Route::post('/editInformationUser', [\App\Http\Controllers\Admin::class,'editInformationUser']);
        Route::get('/deleteUser/{id}', [\App\Http\Controllers\Admin::class,'deleteUser']);
    });

});

Route::post('/register', [\App\Http\Controllers\AuthController::class,'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class,'login']);




