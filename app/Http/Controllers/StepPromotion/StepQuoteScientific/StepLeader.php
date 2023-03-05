<?php

namespace App\Http\Controllers\StepPromotion\StepQuoteScientific;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use App\Models\quoteSc_Member;
use App\Models\table_one_post;
use App\Models\table_two_posts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
class StepLeader extends Controller
{

    public function getUserWaitSendQuoteSc(){
        try {
            $QuoteSc=package_file::select('user_id','QuoteSc','QuoteScReq')->where('QuoteSc',2)->with(['user' => function ($query) {
                $query->select('id','email','name','current_promotion','next_promotion','college','department','exact_jurisdiction','general_jurisdiction','certificate','identification_number','picture');}])->get();
            if(!$QuoteSc){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                ]);
            }
            else {
                return response()->json([
                    'status'=>2,
                    'data'=>$QuoteSc,
                ]);
           }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }
    }

    public function showForSendQuoteScPost(Request $request)
    {
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $package=package_file::select('user_id','QuoteSc')->where('QuoteSc',2)->where('user_id',$valid['user_id'])->first();
            if(!$package){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين'
                ]);
            }else{
                if($package->QuoteSc!=2){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                    ]);
                }else{

                    $table_one_post=table_one_post::select('user_id','search_title','is_single','attachment')->where('user_id',$valid['user_id'])->get();
                    if(!$table_one_post){
                        return response()->json([
                            'status'=>0,
                            'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                        ]);
                    }
                    else  {
                        $table_two_post=table_two_posts::select('user_id','activity_title','is_single','attachment')->where('user_id',$valid['user_id'])->where('is_search',1)->get();
                        if(!$table_two_post){
                            $tables=array();
                            foreach($table_one_post as $table_one){
                                $list = ['title' => $table_one->search_title,'is_single' => $table_one->is_single, 'attachment' => $table_one->attachment,'table' =>'1'];
                                array_push($tables,$list);
                            }
                            return response()->json([
                                'status'=>2,
                                'data'=>$tables,
                            ]);
                        }
                        else{

                            $tables=array();
                            foreach($table_one_post as $table_one){
                                $list = ['title' => $table_one->search_title,'is_single' => $table_one->is_single, 'attachment' => $table_one->attachment,'table' =>'1'];
                                array_push($tables,$list);
                            }

                            foreach($table_two_post as $table_two){
                                $list = ['title' => $table_two->activity_title,'is_single' => $table_two->is_single, 'attachment' => $table_two->attachment,'table' =>'2'];
                                array_push($tables,$list);
                            }
                        return response()->json([
                            'status'=>2,
                            'data'=>$tables,
                        ]);
                    }
                    }
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
            ]);
        }
    }

    public function formationQuoteSc(Request $request)
    {
        $valid=$request->validate([
            'first'=>'required',
            'second'=>'required',
            'third'=>'required',
            'for_user'=>'required',
        ]);
        $Main=user::select('MainQuoteSc','current_promotion','name')->where('id',$valid['first'])->first();//من اجل التحقق اذا كان مشرف على غير لجنة ام لا
        $second=user::select('QuoteSc','current_promotion','name')->where('id',$valid['second'])->first();
        $third=user::select('QuoteSc','current_promotion','name')->where('id',$valid['third'])->first();
        if(!$Main||!$second||!$third){
            return response()->json([
                'status'=>0,
                'aaa'=>1,
            ]);
        }else{
            if($Main->MainQuoteSc!=null){
                $newMian=$Main->MainQuoteSc.$valid['for_user'].',';
                $doneMain=user::where('id',$valid['first'])->update(['MainQuoteSc'=>$newMian]);
            }else{
                $doneMain=user::where('id',$valid['first'])->update(['MainQuoteSc'=>$valid['for_user'].',']);
            }
            if(!$doneMain){
                return response()->json([
                    'status'=>0,
                    'aaa'=>2,
                ]);
            }else{
                if($second->QuoteSc!=null){
                    $newSecond=$second->QuoteSc.$valid['for_user'].',';
                    $doneSecond=user::where('id',$valid['second'])->update(['QuoteSc'=>$newSecond]);
                }else{
                    $doneSecond=user::where('id',$valid['second'])->update(['QuoteSc'=>$valid['for_user'].',']);
                }
                if(!$doneSecond){
                    return response()->json([
                        'status'=>0,
                        'aaa'=>3,
                    ]);
                }else{

                    if($third->QuoteSc!=null){
                        $newThird=$second->QuoteSc.$valid['for_user'].',';
                        $doneThird=user::where('id',$valid['third'])->update(['QuoteSc'=>$newThird]);
                    }else{
                        $doneThird=user::where('id',$valid['third'])->update(['QuoteSc'=>$valid['for_user'].',']);
                    }
                    if(!$doneThird){
                        return response()->json([
                            'status'=>0,
                        ]);
                    }else{
                        $current = Carbon::now();
                        $acepted=package_file::where('user_id',$valid['for_user'])->where('QuoteSc',2)->update(['QuoteSc'=>3,'QuoteScSendLeader'=>$current]);
                        if(!$acepted){
                            return response()->json([
                                'status'=>0,
                            ]);
                        }else {
                            try {
                                Mail::raw('لديك طلب استلال علمي   : ', function ($message) use($valid) {
                                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                    $message->to($valid['first'])->subject('الى رئيس لجنة الاستلال العلمي المحترم يرجى مراجعة نظام الترقيات');
                                    });
                            } catch (\Throwable $th) {

                            }
                            try {
                                Mail::raw('لديك طلب استلال علمي   : ', function ($message) use($valid) {
                                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                    $message->to($valid['second'])->subject('الى عضو لجنة الاستلال العلمي المحترم يرجى مراجعة نظام الترقيات');
                                    });
                            } catch (\Throwable $th) {

                            }
                            try {
                                Mail::raw('لديك طلب استلال علمي   : ', function ($message) use($valid) {
                                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                    $message->to($valid['third'])->subject('الى عضو لجنة الاستلال العلمي المحترم يرجى مراجعة نظام الترقيات');
                                    });
                            } catch (\Throwable $th) {

                            }
                            quoteSc_Member::create([
                                'user_id'=>$valid['for_user'],
                                'MainQuoteSc'=>$valid['first'],
                                'current_promotion1'=>$Main->current_promotion,
                                'name1'=>$Main->name,
                                'SecondQuoteSc'=>$valid['second'],
                                'current_promotion2'=>$second->current_promotion,
                                'name2'=>$second->name,
                                'thirdQuoteSc'=>$valid['third'],
                                'current_promotion3'=>$third->current_promotion,
                                'name3'=>$third->name,
                            ]);
                            return response()->json([
                                'status'=>2,
                            ]);
                        }
                    }
                }
            }
        }

    }


    public function getUserWaitAcceptQuoteSc(){
        try {
            //$pictures=array();
            $QuoteSc=package_file::select('user_id','QuoteSc','QuoteScAttachment','QuoteScSendAtt')->where('QuoteSc',4)->orwhere('QuoteSc',6)->orwhere('QuoteSc',8)->with(['user' => function ($query) {
                $query->select('id','email','name','picture');}])->get();

            if(!$QuoteSc){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                    //'pictures'=>$pictures,
                ]);
            }
            else {
                //سحب الصور
                // foreach($QuoteSc as $single){
                //     if($single->user->picture!=null){
                //         $single_picture2=Storage::disk('public')->get($single->user->picture);
                //         $single_picture2=base64_encode($single_picture2);
                //         array_push($pictures, $single_picture2);
                //     }else{
                //         array_push($pictures, 0);
                //     }
                // }
                return response()->json([
                    'status'=>2,
                    'data'=>$QuoteSc,
                    //'pictures'=>$pictures,
                ]);
           }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }
    }

    public function AcceptQuoteSc(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $user=package_file::select('user_id','QuoteSc')->where('user_id',$valid['user_id'])->first();
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }

        //وجد المستخدم ام لا
        if(!$user){
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }else {
            try {
                $current = Carbon::now();
            //ارسلت من اللجنة
            if($user->QuoteSc==4){
                $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>5,'QuoteScAccRejLeader'=>$current]);
            // المشرف موافق
            }else if($user->QuoteSc==6){
                $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>12,'QuoteScAccRejLeader'=>$current]);
                $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                try {
                    Mail::raw('تقرير استلال علمي', function ($message) use($user) {
                        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال العلمي');
                        });
                } catch (\Throwable $th) {}
            //المشرف لم يوافق
            }else if($user->QuoteSc==8){
                $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>9,'QuoteScAccRejLeader'=>$current]);
            }else{
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',
                ]);
            }
            } catch (\Throwable $th) {
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',
                ]);
            }
            if(!$done){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',
                ]);
            }else{
                return response()->json([
                    'status'=>2,
                    'message'=>'تمت الموافقة بنجاح',
                ]);
            }
        }
    }

    public function RejectQuoteSc(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $user=package_file::select('user_id','QuoteSc')->where('user_id',$valid['user_id'])->first();
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>000,
                'message'=>'حدث خطأ ما',
            ]);
        }
        //وجد المستخدم ام لا
        if(!$user){
            return response()->json([
                'status'=>000,
                'message'=>'حدث خطأ ما',
            ]);
        }else {
            try {
                $current = Carbon::now();
                //ارسلت من اللجنة
                if($user->QuoteSc==4){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>7,'QuoteScAccRejLeader'=>$current]);
                // المشرف موافق
                }else if($user->QuoteSc==6){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>10,'QuoteScAccRejLeader'=>$current]);
                //المشرف لم يوافق
                }else if($user->QuoteSc==8){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>0,'QuoteScAccRejLeader'=>$current]);
                }else{
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ ما',
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',
                ]);
            }

            if(!$done){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',
                ]);
            }else{
                $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                        try {
                            Mail::raw('تقرير استلال علمي', function ($message) use($user) {
                                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال العلمي');
                                });
                        } catch (\Throwable $th) {}
                return response()->json([
                    'status'=>2,
                    'message'=>'تم الرفض ',
                ]);
            }
        }
    }

}
