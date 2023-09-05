<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\RoleHasPermission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function addRole(Request $request){
        $validator = Validator::make($request->all(), [
            'roleName'=>'required',
            'permission'=>['required'],
        ]);
        
        if($validator->fails()){
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }
        
        $role =  Role::create([
            'name' => $request->roleName,
            'slug'=> Str::slug($request->roleName,'_'),
        ]);
        
        $roleID = $role->id;

        $rolePermission ='';
        for($i =0; $i < count($request->permission); $i++){
            $rolePermission =  RoleHasPermission::create([
                'role_id' => $roleID,
                'permissions'=> $request->permission[$i],
            ]);
        }

        $result = RoleHasPermission::where('role_id',$roleID)->get()->toArray();
       
        $permission=[];
        for($i =0; $i < count($result); $i++){
            $permission[$i] =  $result[$i]['permissions'];
        }

        $rolePermission->permissions = $permission;
        
        return hresponse(true, $rolePermission, "Role and Permission Added !!");
    }

    public function viewRole(string $id){
        $data=[];
        $permission=[];
        $role = Role::find($id);
        if($role){
            $role->toArray();
            $data['id'] = $id;
            $data['name'] = $role['name'];

            $role = RoleHasPermission::where('role_id',$id)->get();
            
            for($i =0; $i < count($role); $i++){
                $permission[$i] =  $role[$i]['permissions'];
            }

            $data['permissions'] = $permission;

            return hresponse(true, $data, 'Permissions Assigned to this Role !!');
        }
        return hresponse(false, null, 'Role Not Exist !!');
    }

    public function deleteRole(String $id){
        $role = RoleHasPermission::where('role_id',$id);
        if($role){
            $role->delete();
            Role::find($id)->delete();
            return hresponse(true, null, 'Role Deleted Successfully !!');
        }
        return hresponse(false, null, 'Role Not Found !!');
    }

    public function updateRole(Request $request, string $id){
        $validator = Validator::make($request->all(),[
            'permission'=> ['required'],
        ]);
        if($validator->fails()){
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }
        
        // $role = RoleHasPermission::where('role_id',$id)->get();
        // for($i = 0; $i < count($role); $i++){
        //     $role[$i]['permissions'] = $request->permission[$i];
        // }
        // $role->save();

        DB::table('role_has_permissions')
            ->where('role_id', $id)
            ->update(['permissions' => $request->permission]);
//
        // $role =  $role->toArray();
       
        // $permission=[];
        // for($i =0; $i < count($role); $i++){
        //     $permission[$i] =  $role[$i]['permissions'];
        // }

        // $->permissions = $permission;
        
        // return hresponse(true, $rolePermission, "Role and Permission Added !!");
        // return hresponse(true, $client, 'Client Status Updated !!');

        // return hresponse(true, $role, "Role and Permission Added !!");
    }
}
