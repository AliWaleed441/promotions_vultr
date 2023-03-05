<?php

namespace App\Http\Controllers;
use App\Models\comments_quote;
use Illuminate\Http\Request;

class CommentsQuoteController extends Controller
{
    public function store(Request $request){
        $valid=$request->validate([
            'comment_content'=>'required',
        ]);
        $user=$request->user();
        $comments_quote=comments_quote::create([
            'sender'=>$user->id,
            'name_sender'=>$user->name,
            'comment_content'=>$valid['comment_content'],
        ]);
        if(!$comments_quote){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
            ]);
        }
    }
    public function show(){
        $comments_quote=comments_quote::select('sender','name_sender','comment_content')->get();
        if(!$comments_quote){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
                'data'=>$comments_quote,
            ]);
        }
    }
}
