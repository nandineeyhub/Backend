<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Node\Builder;
use App\Models\State;
use App\Models\City;
class UserController extends Controller
{
    public function register(Request $request){
       $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','confirmed'],
            'phoneNo'=>['required','min:11','numeric'],
            'contactPerson'=>['required'],
            'address'=>['required'],
            'collegeCode'=>['numeric'],
            'stateID'=>['numeric'],
            'countryID'=>['numeric'],
            'cityID'=>['numeric'],
        ]);
    
        if($validator->fails()){
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }
       
        $user =  User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password),
            'phoneNo'=> $request->phoneNo,
            'contactPerson'=> $request->contactPerson,
            'address'=> $request->address,
            'collegeCode'=> $request->collegeCode,
            'stateID'=> $request->stateID,
            'countryID'=> $request->countryID,
            'cityID'=> $request->cityID,
        ]);

        // $token = $user->createToken('mytoken')->plainTextToken;
            
        return hresponse(true, $user, "Registration Succcessful !!");
    }

// ______________________________________________________________ login___________________________________________

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
        if($user){
            if(Hash::check($request->password, $user->password) && $user->status == "Active"){
                $token = $user->createToken('mytoken')->plainTextToken;
                $user->token=$token;
                $user->isSuperAdmin= $user->user_type == 1 ? false : true;
    
                return hresponse(true, $user, 'Login Successful');
            }
            $message = $user->status == "Active" ? 'Wrong Password' : "Your status is not active please contact with SuperAdmin";
    
            return hresponse(false, null, $message);
        }
        return hresponse(false, null, "User Not Found !!");
    }

// _________________________________________________________________________ logout _______________________________________

    public function logout(){
        auth()->user()->tokens()->delete();
        return hresponse(true, null, 'Logout successful !! ');
    }
// _________________________________________________________________________ loggedUser _________________________________

    public function loggedUser(Request $request){
        return hresponse(true, auth()->user(), '');
    }

// ____________________________________________________________________ change Password ___________________________________
    public function changePassword(Request $request){
        $request->validate([
            'password'=>['required','confirmed']
        ]);
        $loggedUser = auth()->user();
        $loggedUser->password = Hash::make($request->password);
        $loggedUser->save();
        return hresponse(false, auth()->user(), 'Password Changed');
    }

//____________________________________________________________________ show All Clients _________________________________________ 
    public function showAllClients(Request $req){
        $res = [];
        $search = $req->search ? $req->search : "";
        $limit = $req->limit ? $req->limit : 17;
        $status = $req->status ? $req->status : "";

        if(!empty($search) && $status == ""){
            $users = User::with(['state','city'])->where('name','LIKE',"%".$search."%")->where('userType','=','1')->paginate($limit);
        }
        else if($status != "" && $search == ""){
            $users = User::with(['state','city'])->where('status',"=","$status")->Where('userType','=','1')->paginate($limit);
        }
        else if(!empty($search) && !empty($status)){
            $users = User::with(['state','city'])->with(['state','city'])->where('status',"=","$status")->where('name','LIKE',"%".$search."%")->Where('userType','=','1')->paginate($limit);
        }
        else{
            $users = User::with(['state','city'])->where('userType','1')->paginate($limit);
        }
        $res['users'] = $users;
        $res['totalRecord'] = $users->count();
        if($users){
    
            return hresponse(true, $res, 'All clients list !!');
        }
        return hresponse(false, null, 'No Record Found !!');
    }

// _________________________________________________________________ Update Client _______________________________________________

    public function updateClient(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required',
            'email'=> ['required','email','unique:users,email,'.$id],
            'phoneNo'=>['required','min:11','numeric'],
            'contactPerson'=>['required'],
            'address'=>['required'],
            'collegeCode'=>['numeric'],
            'stateID'=>['numeric'],
            'countryID'=>['numeric'],
            'cityID'=>['numeric'],
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

    public function showCity($stateID){
       if($stateID){
        $city = City::where('stateID','=',$stateID)->get();
        if(!empty($city->toArray())){
            return hresponse(true, $city, 'All Available Cities list !!');
        }
        return hresponse(false, null, 'City Not Found !!');
       }
        return hresponse(false, null, 'Please select correct State !!');
    }

    public function show(){
        // return User::with('getState')->get();
        return User::with(['state','city'])->get();
    }
}
