<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\table_one_post;
use App\Models\NotificationComment;
use Illuminate\Support\Facades\File;

class TableOnePostController extends Controller
{
    public function store(Request $request)
    {
        $valid=$request->validate([
            'user_id'=>'required',
            'search_title'=>'required',
            'publisher'=>'required',
            'is_impact'=>'required',
            'is_single'=>'required',
            'scores'=>'required',
            'year'=>'required',
            'attachment'=>'nullable',
            'extension_attachment'=>'nullable',
        ]);

            try {
                if($valid['attachment'] != null){
                    $file=base64_decode($valid['attachment']);
                    //$nameFile='1-'.$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
                    $nameFile=$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
                    //Storage::disk('public')->put($nameFile, $file);
                    file_put_contents(public_path().'/storage/'.$nameFile, $file);

                    $post=table_one_post::create([
                        'user_id'=>$valid['user_id'],
                        'search_title'=>$valid['search_title'],
                        'publisher'=>$valid['publisher'],
                        'is_impact'=>$valid['is_impact'],
                        'is_single'=>$valid['is_single'],
                        'scores'=>$valid['scores'],
                        'year'=>$valid['year'],
                        'attachment'=>$nameFile,
                        ]);
                        NotificationComment::create([
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
                    $post=table_one_post::create([
                        'user_id'=>$valid['user_id'],
                        'search_title'=>$valid['search_title'],
                        'publisher'=>$valid['publisher'],
                        'is_impact'=>$valid['is_impact'],
                        'is_single'=>$valid['is_single'],
                        'scores'=>$valid['scores'],
                        'year'=>$valid['year'],
                        'attachment'=>null,
                        ]);
                        NotificationComment::create([
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
            $table_one_post=table_one_post::select('id','user_id','search_title','publisher','is_impact','is_single','scores','year','attachment')->where('user_id',$user->id)->get();
            $paper_notification=collect();

            foreach($table_one_post as $single_post){
                    $paper_notification1=NotificationComment::select('paper_id','user')->where('paper_id',$single_post->id)->get();
                    $paper_notification=$paper_notification->concat($paper_notification1);
            }
            if(!$table_one_post){

                return response()->json([
                    'status'=>1,
                    'data'=>'No post yet'
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                    'data'=>$table_one_post,
                    'notification'=>$paper_notification,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'data'=>$table_one_post,
                'notification'=>$paper_notification,
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
            $table_one_post=table_one_post::select('id','user_id','search_title','publisher','is_impact','is_single','year','scores','attachment')->where('user_id',$valid['user_id'])->get();
            $paper_notification=collect();
            foreach($table_one_post as $single_post){
                        if($valid['kind_of_user']=='supervisor'){
                            $paper_notification1=NotificationComment::select('paper_id','supervisor','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='first_member'){
                            $paper_notification1=NotificationComment::select('paper_id','first_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='second_member'){
                            $paper_notification1=NotificationComment::select('paper_id','second_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='third_member'){
                            $paper_notification1=NotificationComment::select('paper_id','third_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }elseif($valid['kind_of_user']=='forth_member'){
                            $paper_notification1=NotificationComment::select('paper_id','forth_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }else{
                            $paper_notification1=NotificationComment::select('paper_id','fifth_member','user')->where('paper_id',$single_post->id)->get();
                            $paper_notification=$paper_notification->concat($paper_notification1);
                        }
            }
            if(!$table_one_post){
                return response()->json([
                    'status'=>1,
                    'data'=>'No post yet'
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                    'data'=>$table_one_post,
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

        $nameFileInDataBase=table_one_post::select('attachment')->where('id',$valid['id'])->first();

        if($nameFileInDataBase->attachment!=null){
            //$FilesArray=explode(" ",$nameFileInDataBase);
            //$numberFile=count($FilesArray)+1;

            $file=base64_decode($valid['attachment']);
            //$nameFile=$numberFile.'-'.$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
            $nameFile=$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
            //Storage::disk('public')->put($nameFile, $file);
            file_put_contents(public_path().'/storage/'.$nameFile, $file);

            $newNameFileInDataBase=$nameFileInDataBase->attachment.' '.$nameFile;
        }else {

            $file=base64_decode($valid['attachment']);
            //$nameFile='1-'.$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
            $nameFile=$valid['user_id'].'-'.strtotime(now()).'.'.$valid['extension_attachment'];
            //Storage::disk('public')->put($nameFile, $file);
            file_put_contents(public_path().'/storage/'.$nameFile, $file);
            $newNameFileInDataBase=$nameFile;
        }

        $table_one_post=table_one_post::where('id',$valid['id'])->update(['attachment'=>$newNameFileInDataBase]);

        if(!$table_one_post){
            return response()->json([
                'status'=>0,
                'file'=>'wrong'
            ]);
        }
        else  {
            return response()->json([
                'status'=>2,
                'file'=>$table_one_post,
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


    public function changepoints(Request $request)
    {
        $valid=$request->validate([
            'id'=>'required',
            'scores'=>'required',
        ]);
        $user=$request->user();

        $table_one_post=table_one_post::where('id',$valid['id'])->where('user_id',$user->id)->update(['scores'=>$valid['scores']]);
        if(!$table_one_post){
            return response()->json([
                'status'=>0,
                'posts'=>'wrong'
            ]);
        }
        else  {
            //$table_one_post2=table_one_post::select('user_id')->where('id',$valid['id'])->get();
            //$single_post=$table_one_post2[0];
            //TotalScoresOfTables::where('number_of_Table',1)->where('user_id_for_Table',$single_post->user_id)->increment('total_points',$valid['points']);
            return response()->json([
                'status'=>2,
                'posts'=>'done',
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
            $attachment=table_one_post::select('attachment')->where('id',$request->id)->where('user_id',$user->id)->first();
            $FilesArray=explode(" ",$attachment->attachment);
            for($i=0;$i<count($FilesArray);$i++){
                $files=public_path().'/storage/'.$FilesArray[$i];
                File::delete($files);
            }

            $table_one_post=table_one_post::where('id',$request->id)->where('user_id',$user->id)->delete();
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

        $nameFileInDataBase=table_one_post::select('attachment')->where('id',$valid['id'])->first();
        $nameFile=$nameFileInDataBase->attachment;
        if(str_contains($nameFile," ".$valid['attachment'])){
            $newNameFileInDataBase=str_replace(" ".$valid['attachment'],"",$nameFile);
        }else{
            $newNameFileInDataBase=str_replace($valid['attachment'],"",$nameFile);//في حال كان اول ملف ف لا يوجد مسافة
        }

        if(strlen($newNameFileInDataBase)==0){
            $table_one_post=table_one_post::where('id',$valid['id'])->where('user_id',$user->id)->update(['attachment'=>null]);
        }else{
            $table_one_post=table_one_post::where('id',$valid['id'])->where('user_id',$user->id)->update(['attachment'=>$newNameFileInDataBase]);
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
