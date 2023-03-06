<?php

namespace App\Http\Controllers;
use App\Models\comments_sobriety;
use Illuminate\Http\Request;

class CommentsSobrietyController extends Controller
{
    public function store(Request $request){
        $valid=$request->validate([
            'comment_content'=>'required',
        ]);
        $user=$request->user();
        $comments_sobriety=comments_sobriety::create([
            'sender'=>$user->id,
            'name_sender'=>$user->name,
            'comment_content'=>$valid['comment_content'],
        ]);
        if(!$comments_sobriety){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
            ]);
        }
    }
    public function show(Request $request){
        $comments_sobriety=comments_sobriety::select('sender','name_sender','comment_content')->get();
        if(!$comments_sobriety){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
                'data'=>$comments_sobriety,
            ]);
        }
    }

}
