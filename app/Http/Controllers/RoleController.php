<?php
    
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
    
class RoleController extends Controller
{

    function __construct()
    {
        // $this->middleware(['role:super-admin'], ['except' => ['index','store']]);
        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    

    public function index(Request $request)
    {
        $roles = Role::get();
        return response()->json([
            'roles' => $roles
        ]);
    }
    

    public function create()
    {
        $permissions = Permission::get();
        return response()->json([
            'permissions' => $permissions
        ]);
    }
    

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
    
        return response()->json([
            'success' =>'Role created successfully',
            'created role' => $role
        ]);

    }

    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return response()->json([
            'success' =>'Role created successfully',
            'Role' => $role,
            'rolePermissions' => $rolePermissions
        ]);
    
    }
    

    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        // return response()->json([
        //     'role' => $role,
        //     'role permission' => $rolePermissions,
        //     'permission' => $permission,
        // ]);
        $file = public_path()."/تصريح-بالشرف.pdf";
        return response()->download($file);

    }
    

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'permission_name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('permission_name');
        // $role->guard_name = 'api';
        $role->save();
        $permissoins = explode(',',$request->input('permission')) ;
        $role->syncPermissions($permissoins);
    
        return response()->json([
            'role' => $request->input('permission_name'),
            'statut' => $request->input('permission'),
            ''=>$permissoins
        ]);
    }

    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();

        return response()->json([
            'role' => $role,
            'statut' => 'deleted successfuly'
        ]);
    }
}