<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\table_two_comment;
use App\Models\table_two_notification_comments;
use App\Models\table_two_posts;
class TableTwoCommentController extends Controller
{
    public function store(Request $request)
    {
        $valid=$request->validate([
            'paper_id'=>'required',
            'sender'=>'required',
            'comment_content'=>'required',
        ]);

        $paper_comments=table_two_comment::create([
            'paper_id'=>$valid['paper_id'],
            'sender'=>$valid['sender'],
            'comment_content'=>$valid['comment_content'],
        ]);
        $table_one_post=table_two_posts::select('user_id')->where('id',$valid['paper_id'])->first();
        $user=$request->user();

        if($user->user==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>true,'first_member'=>true,'second_member'=>true,'third_member'=>true,'forth_member'=>true,'fifth_member'=>true]);
        }
        else if($user->supervisor==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['first_member'=>true,'second_member'=>true,'third_member'=>true,'forth_member'=>true,'fifth_member'=>true]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>true]);
            }
        }
        else if($user->first_member==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>true,'second_member'=>true,'third_member'=>true,'forth_member'=>true,'fifth_member'=>true]);
        }
        else if($user->second_member==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>true,'first_member'=>true,'third_member'=>true,'forth_member'=>true,'fifth_member'=>true]);
        }
        else if($user->third_member==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>true,'first_member'=>true,'second_member'=>true,'forth_member'=>true,'fifth_member'=>true]);
        }
        else if($user->forth_member==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>true,'first_member'=>true,'second_member'=>true,'third_member'=>true,'fifth_member'=>true]);
        }
        else if($user->fifth_member==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>true,'first_member'=>true,'second_member'=>true,'third_member'=>true,'forth_member'=>true],);
        }

        return response()->json([
            'comments'=>$paper_comments,
        ]);
    }

    public function show(Request $request)
    {
        $valid=$request->validate([
            'paper_id'=>'required',
        ]);

        $table_one_post=table_two_posts::select('user_id')->where('id',$valid['paper_id'])->first();
        $paper_comments=table_two_comment::select('sender','comment_content')->where('paper_id',$valid['paper_id'])->get();

        $user=$request->user();

        if($user->user==1){
            table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
        }
        else if($user->supervisor==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['supervisor'=>false]);
            }
        }

        else if($user->first_member==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['first_member'=>false]);
            }

        }
        else if($user->second_member==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['second_member'=>false]);
            }
        }
        else if($user->third_member==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['third_member'=>false]);
            }
        }
        else if($user->forth_member==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['forth_member'=>false]);
            }
        }
        else if($user->fifth_member==1){
            if($table_one_post->user_id==$user->id){
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['user'=>false]);
            }
            else{
                table_two_notification_comments::where('paper_id',$valid['paper_id'])->update(['fifth_member'=>false]);
            }
        }

        if(!$paper_comments){
            return response()->json(['comments'=>'No comments yet']);
        }
        else {
            return response()->json([
                'comments'=>$paper_comments,
            ]);
        }
    }
}
