<?php

namespace App\Http\Controllers\StepPromotion\StepQuote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use App\Models\table_one_post;
use App\Models\table_two_posts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
class StepLeader extends Controller
{

    public function getUserWaitSendQuote(){
        try {
            //$pictures=array();
            $Quote=package_file::select('user_id','Quote','QuoteReq')->where('Quote',2)->with(['user' => function ($query) {
                $query->select('id','email','name','next_promotion','picture');}])->get();
            if(!$Quote){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                    //'pictures'=>$pictures,
                ]);
            }
            else {
                //سحب الصور
                // foreach($Quote as $single){
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
                    'data'=>$Quote,
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

    public function showForSendQuotePost(Request $request)
    {
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $package=package_file::select('user_id','Quote')->where('Quote',2)->where('user_id',$valid['user_id'])->first();
            if(!$package){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين'
                ]);
            }else{
                if($package->Quote!=2){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                    ]);
                }else{

                    $table_one_post=table_one_post::select('user_id','search_title','attachment')->where('user_id',$valid['user_id'])->get();
                    if(!$table_one_post){
                        return response()->json([
                            'status'=>0,
                            'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                        ]);
                    }
                    else  {
                        $table_two_post=table_two_posts::select('user_id','activity_title','attachment')->where('user_id',$valid['user_id'])->where('is_search',1)->get();
                        if(!$table_two_post){
                            $tables=array();
                            foreach($table_one_post as $table_one){
                                $list = ['title' => $table_one->search_title, 'attachment' => $table_one->attachment,'table' =>'1'];
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
                                $list = ['title' => $table_one->search_title, 'attachment' => $table_one->attachment,'table' =>'1'];
                                array_push($tables,$list);
                            }

                            foreach($table_two_post as $table_two){
                                $list = ['title' => $table_two->activity_title, 'attachment' => $table_two->attachment,'table' =>'2'];
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
    public function SendToQuoteMember(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $current = Carbon::now();
            $acepted=package_file::where('user_id',$valid['user_id'])->where('Quote',2)->update(['Quote'=>3,'QuoteSendLeader'=>$current]);

            if(!$acepted){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',

                ]);

            }else {
                $user=User::select('name')->where('id',$valid['user_id'])->first();
                $quoteMail=User::select('email','name')->where('Quote',1)->Orwhere('MainQuote',1)->get();
                    foreach($quoteMail as $single){
                        try {
                            Mail::raw('لديكم طلب استلال الكتروني من قبل : '.$user->name, function ($message) use($single) {
                                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                $message->to($single->email, $single->name)->subject('الى لجنة الاستلال الالكتروني المحترمة يرجى التفضل يرجى مراجعة نظام الترقيات');
                                });
                        } catch (\Throwable $th) {

                        }
                    }
                return response()->json([
                    'status'=>2,
                    'message'=>'تم الارسال الى لجنة الاستلال الالكتروني',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }

    }

    public function getUserWaitAcceptQuote(){
        try {
            //$pictures=array();
            $Quote=package_file::select('user_id','Quote','QuoteAttachment','QuoteSendAtt')->where('Quote',4)->orwhere('Quote',6)->orwhere('Quote',8)->with(['user' => function ($query) {
                $query->select('id','email','name','picture');}])->get();

            if(!$Quote){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                    //'pictures'=>$pictures,
                ]);
            }
            else {
                //سحب الصور
                // foreach($Quote as $single){
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
                    'data'=>$Quote,
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
                $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>5,'QuoteAccRejLeader'=>$current]);
            // المشرف موافق
            }else if($user->Quote==6){
                $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>12,'QuoteAccRejLeader'=>$current]);
                $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                try {
                    Mail::raw('تقرير استلال الكتروني', function ($message) use($user) {
                        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الاستلال الالكتروني');
                        });
                } catch (\Throwable $th) {}
            //المشرف لم يوافق
            }else if($user->Quote==8){
                $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>9,'QuoteAccRejLeader'=>$current]);
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
                if($user->Quote==4){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>7,'QuoteAccRejLeader'=>$current]);
                // المشرف موافق
                }else if($user->Quote==6){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>10,'QuoteAccRejLeader'=>$current]);
                //المشرف لم يوافق
                }else if($user->Quote==8){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Quote'=>0,'QuoteAccRejLeader'=>$current]);
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
                    'message'=>'تم الرفض ',
                ]);
            }
        }
    }
}
