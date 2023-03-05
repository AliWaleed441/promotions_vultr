<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationComment;
use App\Models\table_one_post;
use App\Models\table_two_notification_comments;
use App\Models\table_two_posts;
class NotificationCommentController extends Controller
{

    public function showforuser(Request $request)
    {
        $user=$request->user();

        $notification_table_one=false;
        $scores_table_one=0;

        $notification_table_two=false;
        $scores_table_two=0;

        $paper_notification_table_one=NotificationComment::select('user_id_for_paper','user')->where('user_id_for_paper',$user->id)->get();
        $scores_table_one=table_one_post::where('user_id',$user->id)->sum('scores');

        if(!$paper_notification_table_one){

            $paper_notification_table_two=table_two_notification_comments::select('user_id_for_paper','user')->where('user_id_for_paper',$user->id)->get();
            $scores_table_two=table_two_posts::where('user_id',$user->id)->sum('scores');

            if(!$paper_notification_table_two){
                return response()->json([
                    'notification_table_one'=>$notification_table_one,
                    'scores_table_one'=>$scores_table_one,
                    'notification_table_two'=>$notification_table_two,
                    'scores_table_two'=>$scores_table_two,
                ]);
            }else{
                foreach($paper_notification_table_two as $i){
                    if($i->user==1){
                        $notification_table_two= true;
                        break;
                    }
                }
                return response()->json([
                    'notification_table_one'=>$notification_table_one,
                    'scores_table_one'=>$scores_table_one,
                    'notification_table_two'=>$notification_table_two,
                    'scores_table_two'=>$scores_table_two,
                ]);
            }
        }
        else {
            foreach($paper_notification_table_one as $i){
                if($i->user==1){
                    $notification_table_one= true;
                    break;
                }
            }

            $paper_notification_table_two=table_two_notification_comments::select('user_id_for_paper','user')->where('user_id_for_paper',$user->id)->get();
            $scores_table_two=table_two_posts::where('user_id',$user->id)->sum('scores');

            if(!$paper_notification_table_two){
                return response()->json([
                    'notification_table_one'=>$notification_table_one,
                    'scores_table_one'=>$scores_table_one,
                    'notification_table_two'=>$notification_table_two,
                    'scores_table_two'=>$scores_table_two,
                ]);
            }else{
                foreach($paper_notification_table_two as $i){
                    if($i->user==1){
                        $notification_table_two= true;
                        break;
                    }
                }
                return response()->json([
                    'notification_table_one'=>$notification_table_one,
                    'scores_table_one'=>$scores_table_one,
                    'notification_table_two'=>$notification_table_two,
                    'scores_table_two'=>$scores_table_two,
                ]);
            }
            
        }
    }

}
