<?php

namespace App\Http\Controllers\StepPromotion\StepQuoteScientific;

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
    public function requestQuoteScTables(Request $request){
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
                'message'=>'تأكد من احتواء البحوث في الجداول على منشورات',
            ]);
        }else if($acepted&&$acepted2){
            try {
                $current = Carbon::now();
                $Quote=package_file::where('user_id',$user->id)->update(['QuoteSc'=>2,'QuoteScReq'=>$current]);
                if($Quote){
                    $quoteMail=User::select('email','name')->where('Leader',1)->first();
                    try {
                        Mail::raw('لديكم طلب استلال علمي من قبل : '.$user->name, function ($message) use($quoteMail,) {
                            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                            $message->to($quoteMail->email, $quoteMail->name)->subject('الى رئيس القسم المحترم يرجى التفضل بالموافقة على ارسال البحوث الى لجنة الاستلال العلمي');
                            });
                    } catch (\Throwable $th) {

                    }

                    return response()->json([
                        'status'=>2,
                        'message'=>'تم ارسال الطلب بنجاح',
                    ]);
                }

                else{
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
                    ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
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
                'status'=>0,
                'message'=>'حدث خطأ عند تأكيد الطلب يرجى المحاولة مرة اخرى او في وقت لاحق',
            ]);
        }

    }





    // public function requestQuoteTableTwo(Request $request){
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
    //         $Quote=package_file::where('user_id',$user->id)->update(['Quote_table_two'=>1]);
    //         if($Quote)
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
