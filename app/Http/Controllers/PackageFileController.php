<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\package_file;
use App\Models\NotificationComment;
use App\Models\table_two_notification_comments;

class PackageFileController extends Controller
{
    public function showForSubervisor(Request $request)
    {
        $user=$request->user();
        $paper_notification_table1=array();
        $paper_notification_table2=array();
        $list_notification_table1=array();
        $list_notification_table2=array();

        $package=package_file::select('user_id','Sobriety','Quote','QuoteSc','SobrietyAttachment','QuoteAttachment','QuoteScAttachment')->where('user_id', '<>' ,$user->id)->with(['user' => function ($query) {
            $query->select('id','email','name','user','supervisor','first_member','second_member','third_member','forth_member','fifth_member','picture');}])->get();
        //ترتيب المستخدمين وجعل المشرفين في الاعلى
        for($i =0;$i<=$package->count()-1;$i++){
            if($package[$i]->user->user==0){
                if($package[$i]->user->supervisor==1){
                    for($j=0;$j<=$i;$j++){
                        $swap=$package[$i];
                        $package[$i]=$package[$j];
                        $package[$j]=$swap;
                    }
                }
                elseif($package[$i]->user->first_member==1){
                    for($j=0;$j<=$i;$j++){
                        $swap=$package[$i];
                        $package[$i]=$package[$j];
                        $package[$j]=$swap;
                    }
                }
                elseif($package[$i]->user->second_member==1){
                    for($j=2;$j<=$i;$j++){
                        $swap=$package[$i];
                        $package[$i]=$package[$j];
                        $package[$j]=$swap;
                    }
                }
                elseif($package[$i]->user->third_member==1){
                    for($j=2;$j<=$i;$j++){
                        $swap=$package[$i];
                        $package[$i]=$package[$j];
                        $package[$j]=$swap;
                    }
                }
                elseif($package[$i]->user->forth_member==1){
                    for($j=4;$j<=$i;$j++){
                        $swap=$package[$i];
                        $package[$i]=$package[$j];
                        $package[$j]=$swap;
                    }
                }
                elseif($package[$i]->user->fifth_member==1){
                    for($j=4;$j<=$i;$j++){
                        $swap=$package[$i];
                        $package[$i]=$package[$j];
                        $package[$j]=$swap;
                    }
                    break;
                }
            }
        }
        if(!$package){
            return response()->json([
                'status'=>1,
                'data'=>'No package yet',
                'notification_table1'=>$list_notification_table1,
                'notification_table2'=>$list_notification_table2,
            ]);
        }
        else {
            //سحب الاشعارات
            if($user->supervisor==1){
                $paper_notification_table1=NotificationComment::select('user_id_for_paper','supervisor')->where('supervisor',1)->get();
                $paper_notification_table2=table_two_notification_comments::select('user_id_for_paper','supervisor')->where('supervisor',1)->get();

            }elseif($user->first_member==1){
                $paper_notification_table1=NotificationComment::select('user_id_for_paper','first_member')->where('first_member',1)->get();
                $paper_notification_table2=table_two_notification_comments::select('user_id_for_paper','first_member')->where('first_member',1)->get();

            }elseif($user->second_member==1){
                $paper_notification_table1=NotificationComment::select('user_id_for_paper','second_member')->where('second_member',1)->get();
                $paper_notification_table2=table_two_notification_comments::select('user_id_for_paper','second_member')->where('second_member',1)->get();

            }elseif($user->third_member==1){
                $paper_notification_table1=NotificationComment::select('user_id_for_paper','third_member')->where('third_member',1)->get();
                $paper_notification_table2=table_two_notification_comments::select('user_id_for_paper','third_member')->where('third_member',1)->get();

            }elseif($user->forth_member==1){
                $paper_notification_table1=NotificationComment::select('user_id_for_paper','forth_member')->where('forth_member',1)->get();
                $paper_notification_table2=table_two_notification_comments::select('user_id_for_paper','forth_member')->where('forth_member',1)->get();

            }elseif($user->fifth_member==1){
                $paper_notification_table1=NotificationComment::select('user_id_for_paper','fifth_member')->where('fifth_member',1)->get();
                $paper_notification_table2=table_two_notification_comments::select('user_id_for_paper','fifth_member')->where('fifth_member',1)->get();

            }
            if(!$paper_notification_table1){
                return response()->json([
                    'status'=>2,
                    'data'=>$package,
                    'notification_table1'=>$list_notification_table1,
                    'notification_table2'=>$list_notification_table2,
                ]);
            }
            else{
                //ترتيب اشعارات الجدول الاول للمستخدمين
                $added=false;
                foreach($package as $i){
                    foreach($paper_notification_table1 as $j){
                        if($i->user_id == $j->user_id_for_paper){
                            array_push($list_notification_table1, 1);
                            $added=true;
                            break;
                        }
                    }
                    if(!$added){
                        array_push($list_notification_table1, 0);
                    }
                    $added=false;
                }


                if(!$paper_notification_table2){
                    return response()->json([
                        'status'=>2,
                        'data'=>$package,
                        'notification_table1'=>$list_notification_table1,
                        'notification_table2'=>$list_notification_table2,
                    ]);
                }else{
                    //ترتيب اشعارات الجدول الثاني للمستخدمين
                    $added=false;
                    foreach($package as $i){
                        foreach($paper_notification_table2 as $j){
                            if($i->user_id == $j->user_id_for_paper){
                                array_push($list_notification_table2, 1);
                                $added=true;
                                break;
                            }
                        }
                        if(!$added){
                            array_push($list_notification_table2, 0);
                        }
                        $added=false;
                    }
                    return response()->json([
                        'status'=>2,
                        'data'=>$package,
                        'notification_table1'=>$list_notification_table1,
                        'notification_table2'=>$list_notification_table2,
                    ]);
                }

            }

       }
    }
    public function showPackageForUser(Request $request){
        $user=$request->user();
        $package=package_file::where('user_id',$user->id)->first();
        if(!$package){
            return response()->json([
                'status'=>0,
                'message'=>'fail',
            ]);
        }else{
            return response()->json([
                'status'=>2,
                'message'=>'success',
                'data'=>$package,
            ]);
        }
    }

}
