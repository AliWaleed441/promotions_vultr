<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\package_file;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
class AuthController extends Controller
{
    function register(Request $request)
    {
            $valid=$request->validate([
                'name'=>'required',
                'identification_number'=>'required',
                'email'=>'required|unique:users',
                'password'=>'required|confirmed',
                'department'=>'required',
                'college'=>'required',
                'certificate'=>'required',
                'general_jurisdiction'=>'required',
                'exact_jurisdiction'=>'required',
                'picture'=>'nullable',
                'extension_picture'=>'nullable',
                'current_promotion'=>'required',
                'date_current_promotion'=>'required',
            ]);
            try {
            if($valid['picture'] != null){
                $finalpicture=base64_decode($valid['picture']);
                $namepicture=$valid['email'].'-'.strtotime(now()).'.'.$valid['extension_picture'];
                //Storage::disk('public')->put($namepicture, $finalpicture);
                file_put_contents(public_path().'/storage/'.$namepicture, $finalpicture);
                $user=User::create([
                    'id'=>$valid['email'],
                    'email'=>$valid['email'],
                    'name'=>$valid['name'],
                    'identification_number'=>$valid['identification_number'],
                    'password'=>Hash::make($valid['password']),
                    'department'=>$valid['department'],
                    'college'=>$valid['college'],
                    'certificate'=>$valid['certificate'],
                    'general_jurisdiction'=>$valid['general_jurisdiction'],
                    'exact_jurisdiction'=>$valid['exact_jurisdiction'],
                    'picture'=>$namepicture,
                    'current_promotion'=>$valid['current_promotion'],
                    'date_current_promotion'=>$valid['date_current_promotion'],
                    'user'=>true
                ]);
            }else{
                $user=User::create([
                    'id'=>$valid['email'],
                    'email'=>$valid['email'],
                    'name'=>$valid['name'],
                    'identification_number'=>$valid['identification_number'],
                    'password'=>Hash::make($valid['password']),
                    'department'=>$valid['department'],
                    'college'=>$valid['college'],
                    'certificate'=>$valid['certificate'],
                    'general_jurisdiction'=>$valid['general_jurisdiction'],
                    'exact_jurisdiction'=>$valid['exact_jurisdiction'],
                    'picture'=>$valid['picture'],
                    'current_promotion'=>$valid['current_promotion'],
                    'date_current_promotion'=>$valid['date_current_promotion'],
                    'user'=>true
                ]);
            }
            $token=$user->createToken($request->deviceId)->plainTextToken;
            $user=User::where('email',$valid['email'])->first();

            return response()->json([
                'status'=>2,
                'data'=>$user,
                //'picture'=>$valid['picture'],
                'token'=>$token,
            ]);
        } catch (\Throwable $th) {
            try {
                User::where('id',$valid['email'])->delete();
            } catch (\Throwable $th) {
                //throw $th;
            }
            return response()->json([
                'status'=>0,
            ]);
        }
    }

    function login(Request $request)
    {

        $valid=$request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);
        try {
            $user=User::where('email',$valid['email'])->first();


        if(!$user){
            return response()->json([
                'status'=>1,
            ]);
        }
        else {
            $password=Hash::check($valid['password'], $user->password);
            if(!$password){
                return response()->json([
                    'status'=>11,
                ]);
            }
            else{
                //$picture=null;
                $token=$user->createToken("$request->deviceId")->plainTextToken;

            if($user->picture != null){
                // $picture=Storage::disk('public')->get($user['picture']);
                // $picture=base64_encode($picture);

                }
                return response()->json([
                    'status'=>2,
                    'data'=>$user,
                    //'picture'=>$picture,
                    'token'=>$token,
                ]);
            }
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'error'=>$th
            ]);
        }


    }

    function logout()
    {
        auth()->user()->tokens()->delete();
       return response()->json(['message'=>'login out success']);
    }

    function editPassword(Request $request)
    {

        $valid=$request->validate([
            'old_password'=>'required',
            'password'=>'required',
            'password_confirmation'=>'required',
        ]);
        try {
            $user=$request->user();
        if(!$user){
            return response()->json([
                'status'=>0,
            ]);
        }
        else {
            $password=Hash::check($valid['old_password'], $user->password);
            if(!$password){
                return response()->json([
                    'status'=>1,
                ]);
            }
            else{
                $user2=user::where('id',$user->id)->update(['password'=>Hash::make($valid['password'])]);

                if(!$user2){
                    return response()->json([
                        'status'=>0,
                    ]);
                }else{
                    return response()->json([
                        'status'=>2,
                    ]);
                }

            }
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }


    }

    function submitToPromotion(Request $request)
    {
        $user=$request->user();
        $user = json_decode($user);
        $current = Carbon::now();

        if($user->current_promotion=='مدرس مساعد'){
            $next_promotion='مدرس';
        }elseif($user->current_promotion=='مدرس'){
            $next_promotion='استاذ مساعد';
        }else $next_promotion='استاذ';

        $user2=user::where('id',$user->id)->update(['next_promotion'=>$next_promotion,'date_next_promotion'=>$current]);

        if(!$user2){
            return response()->json([
                'status'=>0,
            ]);
        }
        else  {
            package_file::create(['user_id'=>$user->id,'email'=>$user->email,'name'=>$user->name]);

            return response()->json([
                'status'=>2,
            ]);
        }
    }

    function changepicture(Request $request)
    {
        $valid=$request->validate([
            'picture'=>'required',
            'extension_picture'=>'required',
        ]);
        $user=$request->user();

        if($user->picture != null){
            //Storage::disk('public')->delete($user->picture);
            $files=public_path().'/storage/'.$user->picture;
            File::delete($files);
        }

        $final_picture=base64_decode($valid['picture']);
        $name_picture=$user->id.'-'.strtotime(now()).'.'.$valid['extension_picture'];
        //Storage::disk('public')->put($name_picture, $final_picture);
        file_put_contents(public_path().'/storage/'.$name_picture, $final_picture);

        $user=user::where('id',$user->id)->update(['picture'=>$name_picture]);

        if(!$user){
            return response()->json(['status'=>1,]);
        }
        else  {
             return response()->json([
                'status'=>2,
                'data'=>$name_picture
            ]);
        }
    }


    function getAllUser()
    {
        try {
            $users=user::where('Admin','<>',1)->get();
            if(!$users){
                return response()->json([
                    'status'=>1,
                    'data'=>'wrong',
                ]);
            }
            else  {
                return response()->json([
                    'status'=>2,
                    'data'=>$users,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
                'data'=>'wrong',
            ]);
        }
    }

    function getInformationUser(Request $request)
    {
        try {
            $user=user::select(
                'name',
                'email',
                'department',
                'identification_number',
                'college',
                'certificate',
                'general_jurisdiction',
                'exact_jurisdiction',
                'current_promotion',
                'next_promotion',
                'date_next_promotion',)->where('id',$request->id)->first();
        //$pictures=array();
        if(!$user){
            return response()->json([
                'status'=>1,
            ]);
        }
        else  {
             return response()->json([
                'status'=>2,
                'data'=>$user,
            ]);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }


}
