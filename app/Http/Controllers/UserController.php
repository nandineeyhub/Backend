<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Node\Builder;
use App\Models\State;
use App\Models\City;
use App\Models\Enquiry;
class UserController extends Controller
{
    public function register(Request $request){
    //    $validator = Validator::make($request->all(), [
    //         'name'=>'required',
    //         'email'=>['required','email','unique:users,email'],
    //         'password'=>['required','confirmed'],
    //         'phoneNo'=>['required','min:11','numeric'],
    //         'contactPerson'=>['required'],
    //         'address'=>['required'],
    //         'collegeCode'=>['numeric'],
    //         'stateID'=>['numeric'],
    //         'countryID'=>['numeric'],
    //         'cityID'=>['numeric'],
    //     ]);
    
    //     if($validator->fails()){
    //         $response = $validator->messages()->first();
    //         return hresponse(false, null, $response);
    //     }
       
    //     $admin =  Admin::create([
    //         'name' => $request->name,
    //         'email'=> $request->email,
    //         'password'=> Hash::make($request->password),
    //         'phoneNo'=> $request->phoneNo,
    //         'contactPerson'=> $request->contactPerson,
    //         'address'=> $request->address,
    //         'collegeCode'=> $request->collegeCode,
    //         'stateID'=> $request->stateID,
    //         'countryID'=> $request->countryID,
    //         'cityID'=> $request->cityID,
    //     ]);

    //     // $token = $Admin->createToken('mytoken')->plainTextToken;
            
    //     return hresponse(true, $admin, "Registration Succcessful !!");
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

        $admin = Admin::where('email',$request->email)->first();
        if($admin){
            if(Hash::check($request->password, $admin->password) && $admin->status == "Active"){
                $token = $admin->createToken('mytoken')->plainTextToken;
                $admin->token=$token;
                $admin->isSuperAdmin= $admin->role == 'SuperAdmin' ? true : false;
    
                return hresponse(true, $admin, 'Login Successful');
            }
            $message = $admin->status == "Active" ? 'Wrong Password' : "Your status is not active please contact with SuperAdmin";
    
            return hresponse(false, null, $message);
        }
        return hresponse(false, null, "Admin Not Found !!");
    }

// _________________________________________________________________________ logout _______________________________________

    public function logout(){
        auth()->user()->tokens()->delete();
        return hresponse(true, null, 'Logout successful !! ');
    }
// _________________________________________________________________________ loggedUser _________________________________

    // public function loggedUser(Request $request){
    //     return hresponse(true, auth()->admin(), '');
    // }

// ____________________________________________________________________ change Password ___________________________________
    // public function changePassword(Request $request){
    //     $request->validate([
    //         'password'=>['required','confirmed']
    //     ]);
    //     $loggedUser = auth()->admin();
    //     $loggedUser->password = Hash::make($request->password);
    //     $loggedUser->save();
    //     return hresponse(false, auth()->admin(), 'Password Changed');
    // }

//____________________________________________________________________ show All Clients _________________________________________ 
    // public function showAllClients(Request $req){
    //     $res = [];
    //     $search = $req->search ? $req->search : "";
    //     $limit = $req->limit ? $req->limit : 17;
    //     $status = $req->status ? $req->status : "";

    //     if(!empty($search) && $status == ""){
    //         $users = Admin::with(['state','city'])->where('name','LIKE',"%".$search."%")->where('userType','=','1')->paginate($limit);
    //     }
    //     else if($status != "" && $search == ""){
    //         $users = Admin::with(['state','city'])->where('status',"=","$status")->Where('userType','=','1')->paginate($limit);
    //     }
    //     else if(!empty($search) && !empty($status)){
    //         $users = Admin::with(['state','city'])->with(['state','city'])->where('status',"=","$status")->where('name','LIKE',"%".$search."%")->Where('userType','=','1')->paginate($limit);
    //     }
    //     else{
    //         $users = Admin::with(['state','city'])->where('userType','1')->paginate($limit);
    //     }
    //     $res['users'] = $users;
    //     $res['totalRecord'] = $users->count();
    //     if($users){
    
    //         return hresponse(true, $res, 'All clients list !!');
    //     }
    //     return hresponse(false, null, 'No Record Found !!');
    // }

// _________________________________________________________________ Update Client _______________________________________________

    // public function updateClient(Request $request, string $id)
    // {
    //     $validator = Validator::make($request->all(),[
    //         'name'=> 'required',
    //         'email'=> ['required','email','unique:users,email,'.$id],
    //         'phoneNo'=>['required','min:11','numeric'],
    //         'contactPerson'=>['required'],
    //         'address'=>['required'],
    //         'collegeCode'=>['numeric'],
    //         'stateID'=>['numeric'],
    //         'countryID'=>['numeric'],
    //         'cityID'=>['numeric'],
    //     ]);
    //     if($validator->fails()){
    //         $response = $validator->messages()->first();
    //         return hresponse(false, null, $response);
    //     }

    //     $client = Admin::find($id);
        
    //     if($client){
    //         $client->first();
    //         $client->update($request->all());
    //         return hresponse(true, $client, 'Client updated Successful !!');
    //     }
    //     else{
    //         return hresponse(false, null, 'Client Not Found !!');
    //     }
    // }
// _________________________________________________________________ Delete Client _______________________________________________
    // public function deleteClient(string $id)
    // {
    //     $client = Admin::find($id);
    //     if($client){
    //         $client->delete();
    //         return hresponse(true, null, 'Client Deleted Successfully !!');
    //     }
    //     return hresponse(false, null, 'Client Not Found !!');
    // }

// _________________________________________________________________ Client Status Update _______________________________________________
    // public function clientStatusUpdate(Request $request, string $id){
    //     if($request->status){
    //         $client = Admin::find($id);
        
    //         if($client){
    //             $client->first();
    //             $client->status = $request->status;
    //             $client->save();
    //             return hresponse(true, $client, 'Client Status Updated !!');
    //         }
    //         else{
    //             return hresponse(false, null, 'Client Not Found !!');
    //         }
    //     }
    //     return hresponse(false, null, 'Please select status !!');
    // }
// _________________________________________________________________Show City _______________________________________________
    // public function showCity($stateID){
    //    if($stateID){
    //     $city = City::where('stateID','=',$stateID)->get();
    //     if(!empty($city->toArray())){
    //         return hresponse(true, $city, 'All Available Cities list !!');
    //     }
    //     return hresponse(false, null, 'City Not Found !!');
    //    }
    //     return hresponse(false, null, 'Please select correct State !!');
    // }
// _________________________________________________________________ enquiry _______________________________________________
    // public function enquiry(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'clientID'=>'required',
    //         'email'=>['required','email','unique:enquiries,email'],
    //         'phone'=>['required','min:11','numeric'],
    //         'name'=>['required'],
    //         'message'=>['required'],
    //         'enquiryDate'=>['required','date',],
    //         'address'=>['required'],
    //         'course'=>['required'],
    //     ]);
    
    //     if($validator->fails()){
    //         $response = $validator->messages()->first();
    //         return hresponse(false, null, $response);
    //     }

    //     $enquiry =  Enquiry::create([
    //         'name' => $request->name,
    //         'email'=> $request->email,
    //         'clientID'=> $request->clientID,
    //         'phone'=> $request->phone,
    //         'address'=> $request->address,
    //         'message'=> $request->message,
    //         'enquiryDate'=> $request->enquiryDate,
    //         'course'=> $request->course,
    //     ]);

    //     if($enquiry){
    //         return hresponse(true, $enquiry, "Enquiry Registerd Succcessfully !!");
    //     }
    //     return hresponse(false, null, "Enquiry Not Added !!");
    // }
}

