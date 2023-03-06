<?php

namespace App\Http\Controllers;
use App\Models\comments_quote_sc;
use App\Models\User;
use Illuminate\Http\Request;

class CommentsQuoteScController extends Controller
{
    public function store(Request $request){
        $valid=$request->validate([
            'receiver'=>'required',
            'name_receiver'=>'required',
            'comment_content'=>'required',
        ]);
        $user=$request->user();
        if($valid['receiver']=='toSuperFromQuote'&&$valid['name_receiver']=='nameToSuperFromQuote'){
            $supervisor=user::select('id','name')->first();
            $valid['receiver']==$supervisor->id;
            $valid['name_receiver']==$supervisor->name;
        }
        $comments_quote_sc=comments_quote_sc::create([
            'sender'=>$user->id,
            'name_sender'=>$user->name,
            'receiver'=>$valid['receiver'],
            'name_receiver'=>$valid['name_receiver'],
            'comment_content'=>$valid['comment_content'],
        ]);
        if(!$comments_quote_sc){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
            ]);
        }
    }

    public function showForSupervisor(Request $request){
        $comments_quote_sc=comments_quote_sc::select('sender','name_sender','receiver','name_receiver','comment_content')->where('sender',$request->quoteId)->Orwhere('receiver',$request->quoteId)->orderBy('created_at','ASC')->get();

        if(!$comments_quote_sc){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
                'data'=>$comments_quote_sc,
            ]);
        }
    }
    public function show(Request $request){
        $user=$request->user();
        $comments_quote_sc=comments_quote_sc::select('sender','name_sender','receiver','name_receiver','comment_content')->where('sender',$user->id)->Orwhere('receiver',$user->id)->orderBy('created_at','ASC')->get();

        if(!$comments_quote_sc){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            return response()->json([
                'status'=>2,
                'data'=>$comments_quote_sc,
            ]);
        }
    }
}
