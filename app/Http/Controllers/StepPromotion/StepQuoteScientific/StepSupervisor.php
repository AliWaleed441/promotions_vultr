<?php

namespace App\Http\Controllers\StepPromotion\StepQuoteScientific;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
class StepSupervisor extends Controller
{
    public function getUserWaitAcceptQuoteSc(){
        try {
            $QuoteSc=package_file::select('user_id','QuoteSc','QuoteScAttachment','QuoteScSendAtt')->where('QuoteSc',4)->orwhere('QuoteSc',5)->orwhere('QuoteSc',7)->with(['user' => function ($query) {
                $query->select('id','email','name','picture');}])->get();

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
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>6,'QuoteScAccRejSub'=>$current]);
                // المشرف موافق
                }else if($user->QuoteSc==5){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>12,'QuoteScAccRejSub'=>$current]);
                    $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                    try {
                        Mail::raw('تقرير الاستلال العلمي', function ($message) use($user) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال العلمي');
                            });
                    } catch (\Throwable $th) {}
                //المشرف لم يوافق
                }else if($user->QuoteSc==7){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>10,'QuoteScAccRejSub'=>$current]);
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
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>8,'QuoteScAccRejSub'=>$current]);
                // المشرف موافق
                }else if($user->QuoteSc==5){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>9,'QuoteScAccRejSub'=>$current]);
                //المشرف لم يوافق
                }else if($user->QuoteSc==7){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc'=>0,'QuoteScAccRejSub'=>$current]);
                    $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                    try {
                        Mail::raw('تقرير الاستلال العلمي', function ($message) use($user) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال العلمي');
                            });
                    } catch (\Throwable $th) {}
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
                    'message'=>'تم الرفض',
                ]);
            }
        }
    }
}
