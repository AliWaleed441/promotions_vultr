<?php

namespace App\Http\Controllers\StepPromotion\StepSobriety;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use App\Models\table_one_post;
use App\Models\table_two_posts;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Exception;
class StepUser extends Controller
{
    public function requestSobrietyTables(Request $request){
        try {
            $user=$request->user();
            $table_one_post=table_one_post::select('attachment')->where('user_id',$user->id)->get();
            $table_two_post=table_two_posts::select('attachment')->where('user_id',$user->id)->where('is_search',1)->get();
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
            ]);
        }

        try {
            $count=0;
        $acepted=true;
        foreach($table_one_post as $single){
            $count++;
            if($single->attachment==null){
                $acepted=false;
                break;
            }
        }
        $acepted2=true;
        foreach($table_two_post as $single){
            if($single->attachment==null){
                $acepted2=false;
                break;
            }
        }
        if($count==0){
            return response()->json([
                'status'=>0,
                'message'=>'يجب ان يحتوي الجدول الاول على بحوث ',
            ]);
        }else if($acepted&&$acepted2){
            try {
                $current = Carbon::now();
                $Sobriety=package_file::where('user_id',$user->id)->update(['Sobriety'=>2,'SobrietyReq'=>$current]);
                if(!$Sobriety){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
                    ]);

                }else{
                    $SubrietyMail=User::select('email','name')->where('Leader',1)->first();
                    try {
                        Mail::raw('لديكم طلب رصانة من قبل : '.$user->name, function ($message) use($SubrietyMail,) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($SubrietyMail->email, $SubrietyMail->name)->subject('الى رئيس القسم المحترم يرجى التفضل بالموافقة على ارسال البحوث الى لجنة الرصانة ');
                            });
                    } catch (\Throwable $th) {

                    }


                    return response()->json([
                        'status'=>2,
                        'message'=>'تم ارسال الطلب بنجاح',
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'statuss'=>11,
                    'status'=>0,
                    'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
                ]);
            }

        }else{
            return response()->json([
            'status'=>0,
            'message'=>'تأكد من احتواء البحوث في الجداول على منشورات',
            ]);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'statuss'=>1,
                'status'=>0,
                'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
            ]);
        }

    }





    // public function requestSobrietyTableTwo(Request $request){
    //     $user=$request->user();
    //     $table_two_posts=table_two_posts::select('attachment')->where('user_id',$user->id)->get();

    //     $count=0;
    //     $acepted=true;
    //     foreach($table_two_posts as $single){
    //         $count++;
    //         if($single->attachment==null){
    //             $acepted=false;
    //             break;
    //         }
    //     }
    //     if($count==0){
    //         return response()->json([
    //             'status'=>'000',
    //             'message'=>'تأكد من احتواء الجدول الثاني على منشورات',
    //         ]);
    //     }else if($acepted){
    //         $Sobriety=package_file::where('user_id',$user->id)->update(['Sobriety_table_two'=>1]);
    //         if($Sobriety)
    //         return response()->json([
    //             'status'=>'201',
    //             'message'=>'تم ارسال الجدول بنجاح',
    //         ]);
    //         else{
    //             return response()->json([
    //                 'status'=>'000',
    //                 'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
    //             ]);
    //         }
    //     }else{
    //         return response()->json([
    //             'status'=>'000',
    //             'message'=>'لديك بعض المنشورات في الجدول الثاني لم يتم ارفاق ملفات فيها',
    //         ]);
    //     }


    //}




}
