<?php

namespace App\Http\Controllers\StepPromotion\StepQuote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\package_file;
use Illuminate\Support\Facades\Storage;
use App\Models\table_one_post;
use App\Models\table_two_posts;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
class StepQuoteMember extends Controller
{

    public function getUserWaitQuote()
    {
        try {
            $pictures=array();
            $Quote=package_file::select('user_id','Quote','QuoteSendLeader','QuoteReq')->where('Quote',3)->with(['user' => function ($query) {
                $query->select('id','email','name','next_promotion','picture');}])->get();

            $leader=User::select('department','college')->where('Leader',1)->first();

        if(!$Quote){
            return response()->json([
                'status'=>1,
                'message'=>'لا يوجد مستخدمين ',
            ]);
        }
        else {

            return response()->json([
                'status'=>2,
                'data'=>$Quote,
                'leader'=>$leader,
                'pictures'=>$pictures,
            ]);
       }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ ما',
            ]);
        }


    }

    public function showForQuotePostTables(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
        ]);
        try {
            $package=package_file::select('user_id','Quote')->where('Quote',3)->where('user_id',$valid['user_id'])->first();
            if(!$package){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                ]);
            }else{
                if($package->Quote!=3){
                    return response()->json([
                        'status'=>0,
                        'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى'
                    ]);
                }else{

                    $table_one_post=table_one_post::select('user_id','search_title','attachment')->where('user_id',$valid['user_id'])->get();
                    if(!$table_one_post){
                        return response()->json([
                            'status'=>0,
                            'message'=>'حدث خطأ ما يرجى المحاولة مرة اخرى',
                        ]);
                    }
                    else  {
                        $table_two_post=table_two_posts::select('user_id','activity_title','attachment')->where('user_id',$valid['user_id'])->where('is_search',1)->get();
                        if(!$table_two_post){//في حال كان الجدول الثاني لا يحتوي على بحوث
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

    public function addFileQuoteForTables(Request $request){
        $valid=$request->validate([
            'user_id'=>'required',
            'attachment'=>'required',
        ]);
        try {
            $file=base64_decode($valid['attachment']);
            $nameFile=$valid['user_id'].'-'.strtotime(now()).'.pdf';
            //Storage::disk('public')->put($nameFile, $file);
            file_put_contents(public_path().'/storage/'.$nameFile, $file);
            $current = Carbon::now();
            $addFile=package_file::where('user_id',$valid['user_id'])->where('Quote',3)->update(['Quote'=>4,'QuoteSendAtt'=>$current,'QuoteAttachment'=>$nameFile]);
            if(!$addFile){
                return response()->json([
                    'status'=>0,
                    'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
                ]);
            }else{
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
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'message'=>'حدث خطأ في ارفاق الملف يرجى المحاولة من جديد',
            ]);
        }
    }


    // public function showForQuotePostTableTwo(Request $request){
    //     $valid=$request->validate([
    //         'user_id'=>'required',
    //     ]);

    //     $package=package_file::select('user_id','Quote_table_two')->where('user_id',$valid['user_id'])->first();

    //     if(!$package){
    //         return response()->json([
    //             'status'=>'000',
    //             'message'=>'fail'
    //         ]);
    //     }else{
    //         if($package->Quote_table_two!=1){
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

    // public function addFileQuoteForTableTwo(Request $request){
    //     $valid=$request->validate([
    //         'user_id'=>'required',
    //         'attachment'=>'required',
    //     ]);
    //     $file=base64_decode($valid['attachment']);
    //     $nameFile=$valid['user_id'].'-'.strtotime(now()).'.pdf';
    //     Storage::disk('public')->put($nameFile, $file);

    //     $addFile=package_file::where('user_id',$valid['user_id'])->update(['Quote_table_two'=>10,'Quote_attachment2'=>$nameFile]);
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
