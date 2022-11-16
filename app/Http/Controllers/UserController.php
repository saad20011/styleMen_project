<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return response()->json([
            "token"=>$data
        ]);
    }
    

    public function create()
    {
        $roles = Role::pluck('name','name')->all();

        return response()->json([
            "roles" => $roles
        ]);
    }
    

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'firstname' => 'required|email|unique:users,email',
            'lastname' => 'required|email|unique:users,email',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
    
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    
        return response()->json([
            "statut" => 'user created successfuly',
        ]);
    }
    

    public function show($id)
    {
        $user = User::find($id);
        
        return response()->json([
            "statut" => $user,
        ]);
    }
    

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
    
        return response()->json([
            "user" => $user,
            "roles" => $roles,
            "user Role" => $userRole,

        ]);
    }
    

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
    
        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }
    
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
    
        $user->assignRole($request->input('roles'));
    
        return response()->json([
            "statut" => 'statut updated successfuly',
        ]);
    }
    

    public function destroy($id)
    {
        User::find($id)->delete();
        return response()->json([
            "statut" => 'statut deleted successfuly',
        ]);
    }
}