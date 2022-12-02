<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\account;
use App\Models\account_user;
use Validator;
use Auth;

class RegisterController extends BaseController
{

    // they register an new user that have already an account id
    public function register_new_user(Request $request,int $is_account=0)
    {
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        if($is_account=1){
            return $user->id;   
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
            $account_user = account_user::where('user_id',Auth::user()->id)
                ->first(['account_id','user_id']);

            $account_user = account_user::create([
                'account_id' => $account_user->account_id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                "status"=>1,
                "data"=>"votre compte utilisateur a été bien crée"
            ], 200); 
        }
    }
   
    // they register an account with default user admin
    public function register_new_account(Request $request)
    {
        // test commit abder
        $validator = Validator::make($request->all(), [
            'account_name' => 'required',
            'account_photo' => 'required',
            'account_photo_dir' => 'required',
            'statut' => '',
            'firstname' => 'required',
            'lastname' => 'required',
            'cin' => 'required',
            'birthday' => 'required',
            'photo' => 'required',
            'photo_dir' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $account_only = collect($request->only( 'statut'))
            ->put('name',$request->account_name)
            // ->put('photo',$request->account_photo)
            // ->put('photo_dir',$request->account_photo_dir)
            ->all();
        $user_request=new Request($request->only('name', 'email', 'password', 'c_password', 'firstname', 'lastname', 'cin', 'birthday', 'status','is_account'));
        $account = account::create($account_only);
        $user_id= $this->register_new_user($user_request,1);
        $request['password'] = bcrypt($request['password']);
        $account_user = account_user::create([
            'account_id' => $account->id,
            'user_id' => $user_id,
        ]);

        return response()->json([
            "status"=>1,
            "data"=>"votre compte a été bien crée"
        ], 200); 
    }

    public function login(Request $request)
    {
        
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details,  Please try again']);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return response(['user' => auth()->user(), 'token' => $token]);

    }
}