<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\table_two_posts;
use App\Models\table_two_notification_comments;
use Illuminate\Support\Facades\File;

class TableTwoPostsController extends Controller

{
    public function store(Request $request)
    {
        $valid=$request->validate([
            'user_id'=>'required',
            'activity_type'=>'required',
            'activity_title'=>'required',
            'scores'=>'required',
            'notes'=>'required',
            'is_single'=>'nullable',
            'is_search'=>'required',
            'year'=>'required',
            'attachment'=>'nullable',
            'extension_attachment'=>'nullable',
        ]);
            try {
                if($valid['attachment'] != null){
                    $file=base64_decode($valid['attachment']);
                    $nameFile=$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
                    //Storage::disk('public')->put($nameFile, $file);
                    file_put_contents(public_path().'/storage/'.$nameFile, $file);

                    $post=table_two_posts::create([
                        'user_id'=>$valid['user_id'],
                        'activity_type'=>$valid['activity_type'],
                        'activity_title'=>$valid['activity_title'],
                        'scores'=>$valid['scores'],
                        'notes'=>$valid['notes'],
                        'is_search'=>$valid['is_search'],
                        'is_single'=>$valid['is_single'],
                        'year'=>$valid['year'],
                        'attachment'=>$nameFile,
                        ]);
                        table_two_notification_comments::create([
                            'paper_id'=>$post->id,
                            'user_id_for_paper'=>$valid['user_id'],
                            'user'=>false,
                            'supervisor'=>false,
                            'first_member'=>false,
                            'second_member'=>false,
                            'third_member'=>false,
                            'forth_member'=>false,
                            'fifth_member'=>false,
                        ]);
                        return response()->json([
                            'status'=>2,
                        ]);
                }
                else{
                    $post=table_two_posts::create([
                        'user_id'=>$valid['user_id'],
                        'activity_type'=>$valid['activity_type'],
                        'activity_title'=>$valid['activity_title'],
                        'scores'=>$valid['scores'],
                        'notes'=>$valid['notes'],
                        'is_search'=>$valid['is_search'],
                        'is_single'=>$valid['is_single'],
                        'year'=>$valid['year'],
                        'attachment'=>null,
                        ]);
                        table_two_notification_comments::create([
                            'paper_id'=>$post->id,
                            'user_id_for_paper'=>$valid['user_id'],
                            'user'=>false,
                            'supervisor'=>false,
                            'first_member'=>false,
                            'second_member'=>false,
                            'third_member'=>false,
                            'forth_member'=>false,
                            'fifth_member'=>false,
                        ]);
                        return response()->json([
                            'status'=>2,
                            'post'=>$post,
                        ]);
                }
            } catch (\Throwable $th) {
                return response()->json([
                    'status'=>0,
                ]);

            }
    }

    public function showForUser(Request $request)
    {
        try {
            $user=$request->user();

            $table_two_post=table_two_posts::select('id','user_id','activity_type','activity_title','scores','is_search','is_single','notes','attachment')->where('user_id',$user->id)->get();
            $paper_notification=collect();
            foreach($table_two_post as $single_post){
                    $paper_notification1=table_two_notification_comments::select('paper_id','user')->where('paper_id',$single_post->id)->get();
                    $paper_notification=$paper_notification->concat($paper_notification1);
            }
            if(!$table_two_post){
                return response()->json([
                    'status'=>1,
                    'data'=>'No post yet'
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                    'data'=>$table_two_post,
                    'notification'=>$paper_notification,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }

    public function showForSupervisor(Request $request)
    {
        $valid=$request->validate([
            'user_id'=>'required',
            'kind_of_user'=>'required',
        ]);
        try {
            $table_two_post=table_two_posts::select('id','user_id','activity_type','activity_title','scores','is_search','is_single','notes','attachment')->where('user_id',$valid['user_id'])->get();
            $paper_notification=collect();
            foreach($table_two_post as $single_post){
                        if($valid['kind_of_user']=='supervisor'){
                            $paper_notification1=table_two_notification_comments::select('paper_id','supervisor','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='first_member'){
                            $paper_notification1=table_two_notification_comments::select('paper_id','first_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='second_member'){
                            $paper_notification1=table_two_notification_comments::select('paper_id','second_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='third_member'){
                            $paper_notification1=table_two_notification_comments::select('paper_id','third_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='forth_member'){
                            $paper_notification1=table_two_notification_comments::select('paper_id','forth_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }else{
                            $paper_notification1=table_two_notification_comments::select('paper_id','fifth_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }
            }
            if(!$table_two_post){
                return response()->json([
                    'status'=>1,
                    'data'=>'No post yet'
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                    'data'=>$table_two_post,
                    'notification'=>$paper_notification,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }

    public function addfile(Request $request)
    {
        $valid=$request->validate([
            'id'=>'required',
            'user_id'=>'required',
            'attachment'=>'required',
            'extension_attachment'=>'required',
        ]);
        try {
            $nameFileInDataBase=table_two_posts::select('attachment')->where('id',$valid['id'])->first();

            if($nameFileInDataBase->attachment!=null){
                $file=base64_decode($valid['attachment']);
                $nameFile=$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
                //Storage::disk('public')->put($nameFile, $file);
                file_put_contents(public_path().'/storage/'.$nameFile, $file);

                $newNameFileInDataBase=$nameFileInDataBase->attachment.' '.$nameFile;
            }else {

                $file=base64_decode($valid['attachment']);
                $nameFile=$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
                //Storage::disk('public')->put($nameFile, $file);
                file_put_contents(public_path().'/storage/'.$nameFile, $file);
                $newNameFileInDataBase=$nameFile;
            }

            $table_one_post=table_two_posts::where('id',$valid['id'])->update(['attachment'=>$newNameFileInDataBase]);

            if(!$table_one_post){
                return response()->json([
                    'status'=>0,
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                    'data'=>$table_one_post,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }



    }

    // public function getfile(Request $request)
    // {
    //     $valid=$request->validate([
    //         'attachment'=>'required',
    //     ]);

    //     $file=Storage::disk('public')->get($valid['attachment']);
    //     $file=base64_encode($file);
    //     if(!$file){
    //         return response()->json(['file'=>'No file']);
    //     }
    //     else  {
    //         return response()->json([
    //             'file'=>$file,
    //         ]);
    //     }
    // }
    public function changePoints(Request $request)
    {
        $valid=$request->validate([
            'id'=>'required',
            'scores'=>'required',
        ]);
        try {
            $table_one_post=table_two_posts::where('id',$valid['id'])->update(['scores'=>$valid['scores']]);
            if(!$table_one_post){
                return response()->json([
                    'status'=>0,
                ]);
            }
            else  {
                //$table_one_post2=table_one_post::select('user_id')->where('id',$valid['id'])->get();
                //$single_post=$table_one_post2[0];
                //TotalScoresOfTables::where('number_of_Table',1)->where('user_id_for_Table',$single_post->user_id)->increment('total_points',$valid['points']);
                return response()->json([
                    'status'=>2,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }
    }

    public function ReverseSearch(Request $request)
    {
        try {
            $user=$request->user();
            $valid=$request->validate([
                'id'=>'required',
                'is_search'=>'required',
                'is_single'=>'nullable',
            ]);
            if($valid['is_search']==0){
                $table_two_posts=table_two_posts::where('id',$request->id)->where('user_id',$user->id)->update(['is_search'=>1,'is_single'=>$valid['is_single']]);
            }else{
                $table_two_posts=table_two_posts::where('id',$request->id)->where('user_id',$user->id)->update(['is_search'=>0,'is_single'=>'لا يوجد']);
            }
            if(!$table_two_posts){
                return response()->json([
                    'status'=>0,
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }
    }

        public function deletePost(Request $request)
    {
        $user=$request->user();
        if(!$request->id){
            return response()->json([
                'status'=>0,
            ]);
        }else{
            $attachment=table_two_posts::select('attachment')->where('id',$request->id)->where('user_id',$user->id)->first();
            $FilesArray=explode(" ",$attachment->attachment);
            for($i=0;$i<count($FilesArray);$i++){
                $files=public_path().'/storage/'.$FilesArray[$i];
                File::delete($files);
            }

            $table_one_post=table_two_posts::where('id',$request->id)->where('user_id',$user->id)->delete();
            if(!$table_one_post){
                return response()->json([
                    'status'=>0,
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                ]);
            }
        }
    }
    public function deleteFile(Request $request)
    {
        $user=$request->user();
        $valid=$request->validate([
            'id'=>'required',
            'attachment'=>'required',
        ]);

        $nameFileInDataBase=table_two_posts::select('attachment')->where('id',$valid['id'])->first();
        $nameFile=$nameFileInDataBase->attachment;
        if(str_contains($nameFile," ".$valid['attachment'])){
            $newNameFileInDataBase=str_replace(" ".$valid['attachment'],"",$nameFile);
        }else{
            $newNameFileInDataBase=str_replace($valid['attachment'],"",$nameFile);//في حال كان اول ملف ف لا يوجد مسافة
        }

        if(strlen($newNameFileInDataBase)==0){
            $table_one_post=table_two_posts::where('id',$valid['id'])->where('user_id',$user->id)->update(['attachment'=>null]);
        }else{
            $table_one_post=table_two_posts::where('id',$valid['id'])->where('user_id',$user->id)->update(['attachment'=>$newNameFileInDataBase]);
        }
        if(!$table_one_post){
            return response()->json([
                'status'=>0,
            ]);
        }
        else  {
            $files=public_path().'/storage/'.$valid['attachment'];
            File::delete($files);
            return response()->json([
                'status'=>2,
            ]);
        }
    }
}
