<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\package_file;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\quoteSc_Member;
class Admin extends Controller
{
    function addMember(Request $request)
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
                'user'=>'required',
                'supervisor'=>'required',
                'first_member'=>'required',
                'second_member'=>'required',
                'third_member'=>'required',
                'forth_member'=>'required',
                'fifth_member'=>'required',
            ]);
            try {
            if($valid['picture'] != null){
                $finalpicture=base64_decode($valid['picture']);
                $namepicture=$valid['email'].'-'.strtotime(now()).'.'.$valid['extension_picture'];
                //Storage::disk('public')->put($namepicture, $finalpicture);
                file_put_contents(public_path().'/storage/'.$namepicture, $finalpicture);
                User::create([
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
                    'user'=>$valid['user'],
                    'supervisor'=>$valid['supervisor'],
                    'first_member'=>$valid['first_member'],
                    'second_member'=>$valid['second_member'],
                    'third_member'=>$valid['third_member'],
                    'forth_member'=>$valid['forth_member'],
                    'fifth_member'=>$valid['fifth_member'],
                ]);
            }else{
                User::create([
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
                    'user'=>$valid['user'],
                    'supervisor'=>$valid['supervisor'],
                    'first_member'=>$valid['first_member'],
                    'second_member'=>$valid['second_member'],
                    'third_member'=>$valid['third_member'],
                    'forth_member'=>$valid['forth_member'],
                    'fifth_member'=>$valid['fifth_member'],
                ]);
            }

            return response()->json([
                'status'=>2,
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
    function getInformationUser(Request $request)
    {
        try {
            $user=user::select(
                'id',
                'name',
                'email',
                'department',
                'identification_number',
                'college',
                'certificate',
                'general_jurisdiction',
                'exact_jurisdiction',
                'current_promotion',
                'date_current_promotion')->where('id',$request->id)->first();
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

    function editInformationUser(Request $request)
    {
        $valid=$request->validate([
            'id'=>'required',
            'name'=>'required',
            'identification_number'=>'required',
            'email'=>'required',
            'department'=>'required',
            'college'=>'required',
            'certificate'=>'required',
            'general_jurisdiction'=>'required',
            'exact_jurisdiction'=>'required',
            'current_promotion'=>'required',
            'date_current_promotion'=>'required',
        ]);
        $user=$request->user();
        $user = json_decode($user);

        if($valid['current_promotion']=='مدرس مساعد'){
            $next_promotion='مدرس';
        }elseif($valid['current_promotion']=='مدرس'){
            $next_promotion='استاذ مساعد';
        }else $next_promotion='استاذ';


        try {
            $user=User::Where('id',$valid['id'])->Update([
                'email'=>$valid['email'],
                'name'=>$valid['name'],
                'identification_number'=>$valid['identification_number'],
                'department'=>$valid['department'],
                'college'=>$valid['college'],
                'certificate'=>$valid['certificate'],
                'general_jurisdiction'=>$valid['general_jurisdiction'],
                'exact_jurisdiction'=>$valid['exact_jurisdiction'],
                'current_promotion'=>$valid['current_promotion'],
                'date_current_promotion'=>$valid['date_current_promotion'],
                'next_promotion'=>$next_promotion,
            ]);
            if(!$user){
                return response()->json([
                    'status'=>0,
                ]);
            }else{
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

    function getSobrietyUser()
    {
        try {
            $Sobriety=user::select('name','email','Sobriety','MainSobriety','picture')->where('Sobriety',1)->Orwhere('MainSobriety',1)->get();

        if(!$Sobriety){
            return response()->json([
                'status'=>1,
            ]);
        }
        else  {
            //ترتيب المستخدمين وجعل رئيس اللجنة في الاعلى
            for($i =0;$i<=$Sobriety->count()-1;$i++){
                if($Sobriety[$i]->MainSobriety==1){
                    for($j=0;$j<=$i;$j++){
                        $swap=$Sobriety[$i];
                        $Sobriety[$i]=$Sobriety[$j];
                        $Sobriety[$j]=$swap;
                    }
                    break;
                }
            }
             return response()->json([
                'status'=>2,
                'data'=>$Sobriety,
            ]);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }
    function getQuoteUser()
    {
        try {
            $Quote=user::select('name','email','Quote','MainQuote','picture')->where('Quote',1)->Orwhere('MainQuote',1)->get();
        if(!$Quote){
            return response()->json([
                'status'=>1,
            ]);
        }
        else  {
            //ترتيب المستخدمين وجعل رئيس اللجنة في الاعلى
            for($i =0;$i<=$Quote->count()-1;$i++){
                if($Quote[$i]->MainQuote==1){
                    for($j=0;$j<=$i;$j++){
                        $swap=$Quote[$i];
                        $Quote[$i]=$Quote[$j];
                        $Quote[$j]=$swap;
                    }
                    break;
                }
            }
             return response()->json([
                'status'=>2,
                'data'=>$Quote,
            ]);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }

    function changeSobrietyUser(Request $request)
    {
        try {
            $valid=$request->validate([
                'first_user_sobriety'=>'required',
                'second_user_sobriety'=>'required',
                'third_user_sobriety'=>'nullable',
                'fourth_user_sobriety'=>'nullable',
                'fifth_user_sobriety'=>'nullable',
            ]);
            $done=user::where('id',$valid['first_user_sobriety'])->where('Leader','<>',1)->update(['MainSobriety'=>true,'Sobriety'=>true,'user'=>false]);
            if($done){
                user::where('sobriety',1)->where('Leader','<>',1)->update(['sobriety'=>false]);
                user::where('id',$valid['second_user_sobriety'])->update(['Sobriety'=>true,'user'=>false]);

                if($valid['third_user_sobriety']!=null){
                    user::where('id',$valid['third_user_sobriety'])->update(['Sobriety'=>true,'user'=>false]);
                    if($valid['fourth_user_sobriety']!=null){
                        user::where('id',$valid['fourth_user_sobriety'])->update(['Sobriety'=>true,'user'=>false]);
                        if($valid['fifth_user_sobriety']!=null){
                            user::where('id',$valid['fifth_user_sobriety'])->update(['Sobriety'=>true,'user'=>false]);
                        }
                    }
                }
            }else{
                return response()->json([
                    'status'=>1,
                ]);
            }

            return response()->json([
                'status'=>2,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }
    function changeQuoteUser(Request $request)
    {
        try {
            $valid=$request->validate([
                'first_user_Quote'=>'required',
                'second_user_Quote'=>'required',
                'third_user_Quote'=>'nullable',
                'fourth_user_Quote'=>'nullable',
                'fifth_user_Quote'=>'nullable',
            ]);

            $done=user::where('id',$valid['first_user_Quote'])->where('Leader','<>',1)->update(['MainQuote'=>true,'Quote'=>true,'user'=>false]);
            if($done){
                user::where('Quote',1)->where('Leader','<>',1)->update(['Quote'=>false]);
                user::where('id',$valid['second_user_Quote'])->update(['Quote'=>true,'user'=>false]);

                if($valid['third_user_Quote']!=null){
                    user::where('id',$valid['third_user_Quote'])->update(['Quote'=>true,'user'=>false]);
                    if($valid['fourth_user_Quote']!=null){
                        user::where('id',$valid['fourth_user_Quote'])->update(['Quote'=>true,'user'=>false]);
                        if($valid['fifth_user_Quote']!=null){
                            user::where('id',$valid['fifth_user_Quote'])->update(['Quote'=>true,'user'=>false]);
                        }
                    }
                }
            }else{
                return response()->json([
                    'status'=>1,
                ]);
            }

            return response()->json([
                'status'=>2,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }

    function getQuoteScUser()
    {
        try {
            $QuoteSc=quoteSc_Member::select('user_id','MainQuoteSc','name1','name2','name3')->with(['user' => function ($query) {
                $query->select('id','name','picture');}])->get();
        if(!$QuoteSc){
            return response()->json([
                'status'=>1,
            ]);
        }
        else  {
             return response()->json([
                'status'=>2,
                'data'=>$QuoteSc,
            ]);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>0,
            ]);
        }

    }
    function deleteUser(Request $request)
    {
        try {
            $done=user::where('id',$request->id)->delete();
        if(!$done){
            return response()->json([
                'status'=>1,
            ]);
        }else{
            package_file::where('user_id',$request->id)->delete();
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
}
