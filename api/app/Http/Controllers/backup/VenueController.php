<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use App\Permission;
use App\Role;
use App\User;
use App\Venue;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class VenueController extends Controller
{
    
    public function postCreate(Request $request){
        $venue = $request->all();
        $v = Validator::make($venue, [
              'name' => 'required|unique:venues|max:255'
        ]);
       
        if($v->passes()){
            try {
                $venue = Venue::create($venue); 
                // Adiciona permissão para o usuário acessar este estabelecimento
                $user = Auth::user();
                $user->venues()->save($venue);

                $response = array(
                  'status' => 'ok',
                  'message' => 'venue_created',
                  'content' => $venue
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

    public function index()
    {
        return response()->json(['auth'=>Auth::user(), 'venues'=>Venue::all()]);
    }

    public function getOne($id)
    {

		$venue = Venue::with(['users', 'menus', 'categories'])->findOrFail($id);
		$response = array(
			'auth' => Auth::user(),
			'venue' => $venue
		);

        return response()->json($response);
    }
    
}    