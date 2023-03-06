<?php

namespace App\Http\Controllers\StepPromotion\StepQuote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
class StepSupervisor extends Controller
{
    public function getUserWaitAcceptQuote(){
        try {
            $Quote=package_file::select('user_id','Quote','QuoteAttachment','QuoteSendAtt')->where('Quote',4)->orwhere('Quote',5)->orwhere('Quote',7)->with(['user' => function ($query) {
                $query->select('id','email','name','picture');}])->get();

            if(!$Quote){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                ]);
            }
            else {

                return response()->json([
                    'status'=>2,
                    'data'=>$Quote,
                ]);
           }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }

    }


    public function AcceptQuote(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $user=package_file::select('user_id','Quote')->where('user_id',$valid['user_id'])->first();
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
                if($user->Quote==4){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>6,'QuoteAccRejSub'=>$current]);
                // المشرف موافق
                }else if($user->Quote==5){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>12,'QuoteAccRejSub'=>$current]);
                    $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                    try {
                        Mail::raw('تقرير الاستلال الالكتروني', function ($message) use($user) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال الالكتروني');
                            });
                    } catch (\Throwable $th) {}
                //المشرف لم يوافق
                }else if($user->Quote==7){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>10,'QuoteAccRejSub'=>$current]);
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

    public function RejectQuote(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $user=package_file::select('user_id','Quote')->where('user_id',$valid['user_id'])->first();
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
                if($user->Quote==4){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>8,'QuoteAccRejSub'=>$current]);
                // المشرف موافق
                }else if($user->Quote==5){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>9,'QuoteAccRejSub'=>$current]);
                //المشرف لم يوافق
                }else if($user->Quote==7){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>0,'QuoteAccRejSub'=>$current]);
                    $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                    try {
                        Mail::raw('تقرير الاستلال الالكتروني', function ($message) use($user) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال الالكتروني');
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
