<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Hash;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class UserController extends Controller
{
    
    //Cadastra um novo usuário
    public function postRegister(Request $request){
        $user = $request->all();
        $v = Validator::make($user, [
              'name' => 'required|max:255',
              'email' => 'required|email|unique:users',
              'password' => 'required|max:255',
        ]);
       
        if($v->passes()){
            try {
                $user['password'] = Hash::make($user['password']);
                $user = User::create($user);    
                $response = array(
                  'status' => 'ok',
                  'message' => 'user_created',
                  'content' => $user
                );
                return response()->json($response);
            } catch(\Exception $e){
                $response = array(
                    'status' => 'error',
                    'message' => 'custom_error',
                    'content' => $e
                );
                return response()->json($response);
            }    
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'validation_error',
                'content' => $v->errors()->all()
            );
            return response()->json($response);
        }
        
    }

}    