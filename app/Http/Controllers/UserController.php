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
            return hresponse(false, null, $response);
        }
       
        $user =  User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
        ]);

        // $token = $user->createToken('mytoken')->plainTextToken;
            
        return hresponse(true, $user, "Registration Succcessful !!");
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>['required','email'],
            'password'=>['required']
        ]);

        if($validator->fails()){
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }

        $user = User::where('email',$request->email)->first();
        if($user && Hash::check($request->password, $user->password) && $user->status == "Active"){
            $token = $user->createToken('mytoken')->plainTextToken;
            $user->token=$token;
            $user->isSuperAdmin= $user->user_type == 1 ? false : true;

            return hresponse(true, $user, 'Login Successful');
        }
        $message = $user->status == "Active" ? 'Wrong Credentials' : "Your status is not active please contact with SuperAdmin";

        return hresponse(false, null, $message);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return hresponse(true, null, 'Logout successful !! ');
    }

    public function loggedUser(Request $request){
        return hresponse(true, auth()->user(), '');
    }

    public function changePassword(Request $request){
        $request->validate([
            'password'=>['required','confirmed']
        ]);
        $loggedUser = auth()->user();
        $loggedUser->password = Hash::make($request->password);
        $loggedUser->save();
        return hresponse(false, auth()->user(), 'Password Changed');
    }

    public function showAllClients(){
        $user = User::where('user_type','1')->get();
        if($user){
            return hresponse(true, $user, 'All clients list !!');
        }
        return hresponse(false, null, 'No Record Found !!');
    }

    public function updateClient(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'email'=> ['required','email','unique:users,email,'.$id],
        ]);
        if($validator->fails()){
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }

        $client = User::find($id);
        
        if($client){
            $client->first();
            $client->update($request->all());
            return hresponse(true, $client, 'Client updated Successful !!');
        }
        else{
            return hresponse(false, null, 'Client Not Found !!');
        }
    }

    public function deleteClient(string $id)
    {
        $client = User::find($id);
        if($client){
            $client->delete();
            return hresponse(true, null, 'Client Deleted Successfully !!');
        }
        return hresponse(false, null, 'Client Not Found !!');
    }

    public function clientStatusUpdate(Request $request, string $id){
        if($request->status){
            $client = User::find($id);
        
            if($client){
                $client->first();
                $client->status = $request->status;
                $client->save();
                return hresponse(true, $client, 'Client Status Updated !!');
            }
            else{
                return hresponse(false, null, 'Client Not Found !!');
            }
        }
        return hresponse(false, null, 'Please select status !!');
    }
}
