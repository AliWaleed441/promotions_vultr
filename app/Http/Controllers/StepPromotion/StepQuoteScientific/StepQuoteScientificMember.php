<?php

namespace App\Http\Controllers\StepPromotion\StepQuoteScientific;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use App\Models\quoteSc_Member;
use Illuminate\Support\Facades\Storage;
use App\Models\table_one_post;
use App\Models\table_two_posts;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
class StepQuoteScientificMember extends Controller
{

    public function getUserWaitQuoteSc(Request $request)
    {
        try {
            $user=$request->user();
            $MainNames=$user->MainQuoteSc;
            $MemberNames=$user->QuoteSc;
            $usersMain=array();
            $usersMember=array();
            if($MainNames!=null){
                $MainNames=explode(',',$MainNames);
                for($i=0;$i<count($MainNames)-1;$i++){
                    $MainQuoteSc=package_file::select('user_id','QuoteSc','QuoteScSendLeader','QuoteScReq')->where('user_id',$MainNames[$i])->where('QuoteSc',3)->with(['user' => function ($query) {
                        $query->select('id','email','name','current_promotion','next_promotion','college','department','exact_jurisdiction','general_jurisdiction','certificate','identification_number','picture');}])->first();
                    $list = ['user_id' => $MainQuoteSc->user_id, 'QuoteSc' => $MainQuoteSc->QuoteSc, 'QuoteScSendLeader' => $MainQuoteSc->QuoteScSendLeader, 'QuoteScReq' => $MainQuoteSc->QuoteScReq];
                    array_push($usersMain,$MainQuoteSc);
                }
            }
            if($MemberNames!=null){
                $MemberNames=explode(',',$MemberNames);
                for($i=0;$i<count($MemberNames)-1;$i++){
                    $MemberQuoteSc=package_file::select('user_id','QuoteSc','QuoteScSendLeader','QuoteScReq')->where('user_id',$MemberNames[$i])->where('QuoteSc',3)->with(['user' => function ($query) {
                        $query->select('id','email','name','current_promotion','next_promotion','college','department','exact_jurisdiction','general_jurisdiction','certificate','identification_number','picture');}])->first();
                    $list = ['user_id' => $MemberQuoteSc->user_id, 'QuoteSc' => $MemberQuoteSc->QuoteSc, 'QuoteScSendLeader' => $MemberQuoteSc->QuoteScSendLeader, 'QuoteScReq' => $MemberQuoteSc->QuoteScReq];
                    array_push($usersMember,$MemberQuoteSc);
                }
            }
            $leader=User::select('name')->where('Leader',1)->first();
            return response()->json([
                'status'=>2,
                'dataMain'=>$usersMain,
                'dataMember'=>$usersMember,
                'leader'=>$leader,
          ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }
    }

    public function showForQuoteScPostTables(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $package=package_file::select('user_id','QuoteSc')->where('QuoteSc',3)->where('user_id',$valid['user_id'])->first();
            if(!$package){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                ]);
            }else{
                if($package->QuoteSc!=3){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                    ]);
                }else{

                    $table_one_post=table_one_post::select('user_id','search_title','is_single','attachment')->where('user_id',$valid['user_id'])->get();
                    if(!$table_one_post){
                        return response()->json([
                            'status'=>0,
                            'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى',
                        ]);
                    }
                    else  {
                        $table_two_post=table_two_posts::select('user_id','activity_title','is_single','attachment')->where('user_id',$valid['user_id'])->where('is_search',1)->get();
                        if(!$table_two_post){//في حال كان الجدول الثاني لا يحتوي على بحوث
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

    public function getQuoteScMember(Request $request)
    {
        $user=quoteSc_Member::where('user_id',$request->id)->first();
        if(!$user){
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
            ]);
        }else{
            return response()->json([
                'status'=>2,
                'data'=>$user,
            ]);
        }
    }

    public function addFileQuoteScForTables(Request $request){
        $MainOrNot=$request->user();
        $valid=$request->validate([
            'user_id'=>'required',
            'attachment'=>'required',
        ]);
        try {
            $check=quoteSc_Member::select('MainQuoteSc')->where('user_id',$valid['user_id'])->first();
            if(!$check){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
                    'messaaaage'=>$check,
                ]);
            }else if($check->MainQuoteSc!=$MainOrNot->id){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
                    'messaaaage'=>$MainOrNot,
                ]);
            }else{
                $file=base64_decode($valid['attachment']);
                $nameFile=$valid['user_id'].'-'.strtotime(now()).'.pdf';
                file_put_contents(public_path().'/storage/'.$nameFile, $file);
                $current = Carbon::now();
                $addFile=package_file::where('user_id',$valid['user_id'])->where('QuoteSc',3)->update(['QuoteSc'=>4,'QuoteScSendAtt'=>$current,'QuoteScAttachment'=>$nameFile]);
                if(!$addFile){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
                    ]);
                }else{
                    $users=user::select('id','MainQuoteSc','QuoteSc')->get();
                    foreach($users as $single){
                        if(str_contains($single->MainQuoteSc,$valid['user_id'].',')){
                            $newMain=str_replace($valid['user_id'].",","",$single->MainQuoteSc);
                            if($newMain==""){
                                user::where('id',$single->id)->update(['MainQuoteSc'=>null]);
                            }else{
                                user::where('id',$single->id)->update(['MainQuoteSc'=>$newMain]);
                            }
                        }
                        if(str_contains($single->QuoteSc,$valid['user_id'].',')){
                            $newSecondThird=str_replace($valid['user_id'].",","",$single->QuoteSc);
                            if($newSecondThird==""){
                                user::where('id',$single->id)->update(['QuoteSc'=>null]);
                            }else{
                                user::where('id',$single->id)->update(['QuoteSc'=>$newSecondThird]);
                            }

                        }
                    }
                    $user=User::select('name')->where('id',$valid['user_id'])->first();
                    $quoteMail=User::select('email','name')->where('Leader',1)->Orwhere('supervisor',1)->get();
                        foreach($quoteMail as $single){
                            try {
                                Mail::raw('لديكم تقرير استلال الكتروني لطالب الترقية  : '.$user->name, function ($message) use($single) {
                                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                                    $message->to($single->email, $single->name)->subject('يرجى الاطلاع على تقرير الاستلال الالكتروني');
                                    });
                            } catch (\Throwable $th) {

                            }
                        }
                    return response()->json([
                        'status'=>2,
                        'message'=>'تم ارسال الملف بنجاح',
                    ]);
                }
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
            ]);
        }
    }


    // public function showForQuoteScPostTableTwo(Request $request){
    //     $valid=$request->validate([
    //         'user_id'=>'required',
    //     ]);

    //     $package=package_file::select('user_id','QuoteSc_table_two')->where('user_id',$valid['user_id'])->first();

    //     if(!$package){
    //         return response()->json([
    //             'status'=>'000',
    //             'message'=>'fail'
    //         ]);
    //     }else{
    //         if($package->QuoteSc_table_two!=1){
    //             return response()->json([
    //                 'status'=>'000',
    //                 'message'=>'حدث خطأ ما يرجى المحاولة في وقت لاحق'
    //             ]);
    //         }else{
    //             $table_two_posts=table_two_posts::select('id','user_id','activity_type','activity_title','scores','notes','attachment')->where('user_id',$valid['user_id'])->get();
    //             if(!$table_two_posts){
    //                 return response()->json([
    //                     'status'=>'000',
    //                     'message'=>'حدث خطأ ما يرجى المحاولة في وقت لاحق'
    //                 ]);
    //             }
    //             else  {
    //                 return response()->json([
    //                     'status'=>'201',
    //                     'message'=>'success',
    //                     'data'=>$table_two_posts,
    //                 ]);
    //             }
    //         }
    //     }

    // }

    // public function addFileQuoteScForTableTwo(Request $request){
    //     $valid=$request->validate([
    //         'user_id'=>'required',
    //         'attachment'=>'required',
    //     ]);
    //     $file=base64_decode($valid['attachment']);
    //     $nameFile=$valid['user_id'].'-'.strtotime(now()).'.pdf';
    //     Storage::disk('public')->put($nameFile, $file);

    //     $addFile=package_file::where('user_id',$valid['user_id'])->update(['QuoteSc_table_two'=>10,'QuoteSc_attachment2'=>$nameFile]);
    //     if(!$addFile){
    //         return response()->json([
    //             'status'=>'000',
    //             'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status'=>'201',
    //             'message'=>'تم ارفاق ملف الرصانة بنجاح',
    //         ]);
    //     }
    // }



}
