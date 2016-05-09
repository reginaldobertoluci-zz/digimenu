<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Permission;
use App\Role;
use App\User;
use App\Venue;
use App\Menu;
use App\Section;
use App\Item;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class ItemController extends Controller
{
    
    public function postCreate(Request $request, $venue, $menu, $section){
        $item = $request->all();
        $v = Validator::make($item, [
              'name' => 'required|max:255',
              'price' =>'required'
        ]);
       
        if($v->passes()){
            try {
                //Verifica se o usuário tem permissão para acessar este estabelecimento
                $venue = Venue::find($venue);
                $users = $venue->users->where('id', Auth::user()->id)->first();
                if(count($users) > 0){
                  $menu = Menu::find($menu);
                  $section = Section::find($section);

                  $item = Item::create($item); 
                  
                  // Associa o item a seção
                  $item->section()->associate($section);
                  $item->save();   

                  $response = array(
                    'status' => 'ok',
                    'message' => 'item_created',
                    'content' => ['id' => $item->id, 'name' => $item->name, 'price' => $item->price]
                  );

                  return response()->json($response);
                  
                // Se o usuário não tem permissão para acessar este estabelecimento...  
                } else {
                  $response = array(
                    'status' => 'error',
                    'message' => 'no_permission',
                    'content' => 'You do not have permission to create items on this section'
                  );
                  return response()->json($response);
                }  
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