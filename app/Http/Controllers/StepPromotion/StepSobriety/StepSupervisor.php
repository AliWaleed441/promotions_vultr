<?php

namespace App\Http\Controllers\StepPromotion\StepSobriety;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
class StepSupervisor extends Controller
{
    public function getUserWaitAcceptSobriety(){
        try {
            $Sobriety=package_file::select('user_id','Sobriety','SobrietyAttachment','SobrietySendAtt')->where('Sobriety',4)->orwhere('Sobriety',5)->orwhere('Sobriety',7)->with(['user' => function ($query) {
                $query->select('id','email','name','picture');}])->get();

            if(!$Sobriety){
                return response()->json([
                    'status'=>201,
                    'message'=>'لا يوجد مستخدمين حالياً',
                ]);
            }
            else {

                return response()->json([
                    'status'=>2,
                    'data'=>$Sobriety,
                ]);
           }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }

    }

    public function AcceptSobriety(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $user=package_file::select('user_id','Sobriety')->where('user_id',$valid['user_id'])->first();
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
                if($user->Sobriety==4){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>6,'SobrietyAccRejSub'=>$current]);
                // رئيس القسم موافق
                }else if($user->Sobriety==5){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>12,'SobrietyAccRejSub'=>$current]);
                    $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                    try {
                        Mail::raw('تقرير الرصانة', function ($message) use($user) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الرصانة');
                            });
                    } catch (\Throwable $th) {}
                //رئيس القسم لم يوافق
                }else if($user->Sobriety==7){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>10,'SobrietyAccRejSub'=>$current]);
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

    public function RejectSobriety(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $user=package_file::select('user_id','Sobriety')->where('user_id',$valid['user_id'])->first();
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
                if($user->Sobriety==4){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>8,'SobrietyAccRejSub'=>$current]);
                // المشرف موافق
                }else if($user->Sobriety==5){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>9,'SobrietyAccRejSub'=>$current]);
                //المشرف لم يوافق
                }else if($user->Sobriety==7){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>0,'SobrietyAccRejSub'=>$current]);
                    $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                    try {
                        Mail::raw('تقرير الرصانة', function ($message) use($user) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الرصانة');
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
                    'message'=>'تمت الرفض',
                ]);
            }
        }
    }
}
