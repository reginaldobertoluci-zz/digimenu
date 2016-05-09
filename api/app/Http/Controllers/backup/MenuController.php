<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Permission;
use App\Role;
use App\User;
use App\Venue;
use App\Menu;
use App\Item;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class MenuController extends Controller
{
    
    public function postCreate(Request $request, $venue){
        $menu = $request->all();
        $v = Validator::make($menu, [
              'name' => 'required|max:255'
        ]);
       
       
        if($v->passes()){
            //try {
                //Verifica se o usuário tem permissão para acessar este estabelecimento
                $venue = Venue::find($venue);
                $users = $venue->users->where('id', Auth::user()->id)->first();
                if(count($users) > 0){
                  $menu['qrcode'] = str_random(16);
                  $menu = Menu::create($menu); 
                  
                  // Associo o menu ao estabelecimento
                  $menu->venue()->associate($venue);
                  $menu->save();   

                  $response = array(
                    'status' => 'ok',
                    'message' => 'menu_created',
                    'content' => ['id' => $menu->id, 'name' => $menu->name, 'qrcode'=>$menu->qrcode]
                  );

                  return response()->json($response);
                  
                // Se o usuário não tem permissão para acessar este estabelecimento...  
                } else {
                  $response = array(
                    'status' => 'error',
                    'message' => 'no_permission',
                    'content' => 'You do not have permission to create menus on this venue'
                  );
                  return response()->json($response);
                }  
           // } catch(\Exception $e){
           //     $response = array(
           //         'status' => 'error',
           //         'message' => 'custom_error',
           //         'content' => $e
           //     );
           // /    return response()->json($response);
           // }    
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
        return response()->json(['auth'=>Auth::user(), 'menus'=>Menu::all()]);
    }

    public function getOne($id)
    {

		$menu = Menu::with(['sections'])->findOrFail($id);

		$response = array(
			'auth' => Auth::user(),
			'menu' => $menu
		);

        return response()->json($response);
    }

    public function getQRCode($code)
    {

        $menu = Menu::with([
            'sections' => function($q){
                $q->orderBy('order', 'asc');
            },
            'sections.items'
        ])->where('qrcode', $code)->first();

        $response = array(
            'menu' => $menu
        );

        return response()->json($response);
    }

}    