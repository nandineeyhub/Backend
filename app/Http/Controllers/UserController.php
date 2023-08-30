<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function register(Request $request){

       $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','confirmed'],
        ]);
    
        if($validator->fails()){
            $response = $validator->messages()->first();
            return response([
                'message'=> $response,
                'data'=> null,
                'isSuccess'=> false,
            ],200);
        }

        // $request->validate([
        //     'name'=>'required',
        //     'email'=>['required','email','unique:users,email'],
        //     'password'=>['required','confirmed'],
        // ]);
       
        $user =  User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        // $token = $user->createToken('mytoken')->plainTextToken;
            
        return response([
            'message'=>'Registration Successful',
            'data'=> $user,
            'isSuccess'=> true,
        ],200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>['required','email'],
            'password'=>['required']
        ]);

        if($validator->fails()){
            $response = $validator->messages()->first();
            return response([
                'message'=> $response,
                'data'=> null,
                'isSuccess'=> false,
            ],200);
        }

        $user = User::where('email',$request->email)->first();
        if($user && Hash::check($request->password, $user->password)){
            $token = $user->createToken('mytoken')->plainTextToken;
            $user->token=$token;
            $user->isSuperAdmin= $user->user_type == 1 ? false : true;
            return response([    
                'message'=>'Login Successful',
                'data'=> $user,
                'isSuccess'=> true,
            ],200);
        }
        return response([
            'message'=>'Wrong Credentials',
            'isSuccess'=> false,
            'data'=>null
        ],200);
    }

    public function logout(){
        auth()->user()->tokens()->delete(); 
        return response([
            'message' => 'Logout successful !! ',
            'isSuccess' => true,
            'data'=>null,
        ],200); 
    }

    public function loggedUser(Request $request){
        return response([
            'isSuccess'=> true,
            'data'=>auth()->user()
        ],200);
    }

    public function changePassword(Request $request){
        $request->validate([
            'password'=>['required','confirmed']
        ]);
        $loggedUser = auth()->user();
        $loggedUser->password = Hash::make($request->password);
        $loggedUser->save();
        return response([
            'isSuccess'=> false,
            'message'=>"Password Changed",
        ],200);
    }
}
