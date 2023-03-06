<?php

namespace App\Http\Controllers\StepPromotion\StepSobriety;

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
    public function getUserWaitSendSobriety(){
        try {
            //$pictures=array();
            $Sobriety=package_file::select('user_id','Sobriety','SobrietyReq')->where('Sobriety',2)->with(['user' => function ($query) {
                $query->select('id','email','name','next_promotion','picture');}])->get();
            if(!$Sobriety){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                    //'pictures'=>$pictures,
                ]);
            }
            else {
                //سحب الصور
                // foreach($Sobriety as $single){
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
                    'data'=>$Sobriety,
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

    public function showForSendSobrietyPostTables(Request $request)
    {
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $package=package_file::select('user_id','Sobriety')->where('Sobriety',2)->where('user_id',$valid['user_id'])->first();
            if(!$package){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين'
                ]);
            }else{
                if($package->Sobriety!=2){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                    ]);
                }else{

                    $table_one_post=table_one_post::select('search_title','publisher','year','attachment')->where('user_id',$valid['user_id'])->get();
                    if(!$table_one_post){
                        return response()->json([
                            'status'=>0,
                            'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                        ]);
                    }
                    else  {
                        $table_two_post=table_two_posts::select('activity_title','activity_type','year','attachment')->where('user_id',$valid['user_id'])->where('is_search',1)->get();
                        if(!$table_two_post){
                            return response()->json([
                                'status'=>0,
                                'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                            ]);
                        }
                        else{

                            $tables=array();
                            foreach($table_one_post as $table_one){
                                $list = ['title' => $table_one->search_title,'publisher' => $table_one->publisher,'year' => $table_one->year, 'attachment' => $table_one->attachment];
                                array_push($tables,$list);
                            }

                            foreach($table_two_post as $table_two){
                                $list = ['title' => $table_two->activity_title,'publisher' => $table_two->activity_type,'year' => $table_two->year, 'attachment' => $table_two->attachment];
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
    public function SendToSobrietyMember(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);

        try {
            $current = Carbon::now();
            $acepted=package_file::where('user_id',$valid['user_id'])->where('Sobriety',2)->update(['Sobriety'=>3,'SobrietySendLeader'=>$current]);

            if(!$acepted){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما',

                ]);

            }else {
                $user=User::select('name')->where('id',$valid['user_id'])->first();
                $quoteMail=User::select('email','name')->where('Sobriety',1)->Orwhere('MainSobriety',1)->get();
                    foreach($quoteMail as $single){
                        try {
                            Mail::raw('لديكم طلب الرصانة من قبل : '.$user->name, function ($message) use($single) {
                                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                $message->to($single->email, $single->name)->subject('الى لجنة الرصانةالمحترمة يرجى مراجعة نظام الترقيات');
                                });
                        } catch (\Throwable $th) {

                        }
                    }
                return response()->json([
                    'status'=>2,
                    'message'=>'تم الارسال الى لجنة الرصانة',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }

    }

    public function getUserWaitAcceptSobriety(){
        try {
            //$pictures=array();
            $Sobriety=package_file::select('user_id','Sobriety','SobrietyAttachment','SobrietySendAtt')->where('Sobriety',4)->orwhere('Sobriety',6)->orwhere('Sobriety',8)->with(['user' => function ($query) {
                $query->select('id','email','name','picture');}])->get();

            if(!$Sobriety){
                return response()->json([
                    'status'=>1,
                    'message'=>'لا يوجد مستخدمين حالياً',
                    //'pictures'=>$pictures,
                ]);
            }
            else {
                //سحب الصور
                // foreach($Sobriety as $single){
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
                    'data'=>$Sobriety,
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
                $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>5,'SobrietyAccRejLeader'=>$current]);
            // المشرف موافق
            }else if($user->Sobriety==6){
                $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>12,'SobrietyAccRejLeader'=>$current]);
                $user=User::select('name','email')->where('id',$valid['user_id'])->first();
                try {
                    Mail::raw('تقرير الرصانة', function ($message) use($user) {
                        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $message->to($user->email, $user->name)->subject('يرجى التحقق من نظام الترقيات بخصوص الرصانة');
                        });
                } catch (\Throwable $th) {}
            //المشرف لم يوافق
            }else if($user->Sobriety==8){
                $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>9,'SobrietyAccRejLeader'=>$current]);
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
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>7,'SobrietyAccRejLeader'=>$current]);
                // المشرف موافق
                }else if($user->Sobriety==6){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>10,'SobrietyAccRejLeader'=>$current]);
                //المشرف لم يوافق
                }else if($user->Sobriety==8){
                    $done=package_file::where('user_id',$valid['user_id'])->update(['Sobriety'=>0,'SobrietyAccRejLeader'=>$current]);
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
                    'message'=>'تم الرفض ',
                ]);
            }
        }
    }
}
