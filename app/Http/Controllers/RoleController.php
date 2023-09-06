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
    public function addRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roleName' => 'required',
            'permission' => ['required'],
        ]);

        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }

        $role =  Role::create([
            'name' => $request->roleName,
            'slug' => Str::slug($request->roleName, '_'),
        ]);

        $data = [];
        $permissions = [];
        if ($role) {
            $data['roleID'] = $role->id;
            foreach($request->permission as $i=>$val){
                RoleHasPermission::create([
                    'role_id' => $role->id,
                    'permissions' => $request->permission[$i],
                ]);
                $permissions[$i] = $request->permission[$i];
            }
            $data['permissions'] = $permissions;
            return hresponse(true, $data, "Role and Permission Added !!");
        }
        return hresponse(false, null, "Role is not Added !!");
    }

    public function viewRole(string $id)
    {
        $role = Role::find($id);
        if ($role) {
            $data = Role::with('permissions:role_id,permissions')->findOrFail($id);
            return hresponse(true, $data, 'List of Permissions Assigned to this Role !!');
        }
        return hresponse(false, null, 'Role Not Exist !!');
    }

    public function deleteRole(String $id)
    {
        $role = RoleHasPermission::where('role_id', $id);
        if ($role) {
            $role->delete();
            Role::find($id)->delete();
            return hresponse(true, null, 'Role Deleted Successfully !!');
        }
        return hresponse(false, null, 'Role Not Found !!');
    }

    public function updateRole(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permission' => ['required'],
        ]);
        if ($validator->fails()) {
            $response = $validator->messages()->first();
            return hresponse(false, null, $response);
        }
        $role = Role::find($request->id);
        if ($role) {
            $role->name = $request->name;
            $role->save();
            if ($role) {
                RoleHasPermission::where('role_id', $role->id)->delete();
                foreach ($request->permission as  $key => $row) {
                    RoleHasPermission::create([
                        'role_id' => $role->id,
                        'permissions' => $row,
                    ]);
                }
            }
            $updatedData = Role::with('permissions:role_id,permissions')->findOrFail($request->id);
            
            return hresponse(true, $updatedData, "Role and Permission Added !!");
        }
        return hresponse(false, null, "Role not Found !!");
    }
}
