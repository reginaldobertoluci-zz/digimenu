<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Venue;
use App\Menu;
use App\User;

class MenuController extends Controller
{
    public function index($venueId)
    {
        try {
        	$venue = Venue::find($venueId);
        	if($venue){
        		$response = array(
                  'status' => 'ok',
                  'message' => 'get_menus',
                  'content' => $venue->menus
            	);
            	return response()->json($response);
        	} else {
        		$response = array(
	        		'status' => 'error',
    	        	'message' => 'not_found',
        	    	'content' => 'No menu found with this parameters'
           		);
	        	return response()->json($response, 404);	
        	}
        } catch(\Exception $e){
        	$response = array(
                    'status' => 'error',
                    'message' => 'custom_error',
                    'content' => $e->getMessage()
            );
        	return response()->json($response, 500);
        }	
        
    }

    public function store(Request $request, $venueId){
        $menu = $request->all();
        $venue = Venue::find($venueId);

        if($venue){
        	//Verifica se o usuário tem permissão para acessar este estabelecimento
			$users = $venue->users->where('id', Auth::user()->id)->first();
            if(count($users) > 0){
 				$v = Validator::make($menu, [
              		'name' => 'required|max:255'
        		]);
                if($v->passes()){
                	$menu['qrcode'] = str_random(16);
                	$menu = Menu::create($menu); 
                  
                  	// Associa o menu ao estabelecimento
                  	$menu->venue()->associate($venue);
                  	$menu->save();   

                  	$response = array(
                    	'status' => 'ok',
                    	'message' => 'menu_created',
                    	'content' => ['id' => $menu->id, 'name' => $menu->name, 'qrcode'=>$menu->qrcode]
                  	);
	                return response()->json($response);
		        } else {
            		$response = array(
                		'status' => 'error',
                		'message' => 'validation_error',
                		'content' => $v->errors()->all()
            		);
            		return response()->json($response);
        		}
            // Se o usuário não tem permissão para acessar este estabelecimento...  
            } else {
                $response = array(
                  'status' => 'error',
                  'message' => 'no_permission',
                  'content' => 'You do not have permission to create menus on this venue'
                );
                return response()->json($response);
            }  
        } else {
            $response = array(
	        	'status' => 'ok',
    	        'message' => 'not_found',
        	    'content' => 'No venue found with this parameters'
           	);
	        return response()->json($response, 404);
        }    
       
    }    

    public function update(Request $request, $venueId, $menuId)
    {
        $data = $request->all();
        try {
            $venue = Venue::find($venueId);
            if($venue){
            	$users = $venue->users->where('id', Auth::user()->id)->first();
	            if(count($users) > 0){
    	        	$menu = Venue::where('id',$venueId)->whereHas('menus', function($q) use ($menuId)
					{
						$q->where('id', $menuId);
					})->first();
    	        	if($menu){
        	    		$v = Validator::make($data, [
            	        'name' => 'required|max:255'
                		]);
	                	if($v->passes()){
							$menu->update($data); 
        	            	$response = array(
            	         		'status' => 'ok',
                	      		'message' => 'menu_updated',
                    	  		'content' => $menu
	                    	);	
    	                	return response()->json($response);
        	         	} else {
            	        	$response = array(
                	       		'status' => 'error',
                    	   		'message' => 'validation_error',
                       			'content' => $v->errors()->all()
	                    	);
    	                	return response()->json($response);
        	        	}
            		} else {
               			$response = array(
	               			'status' => 'ok',
    	           			'message' => 'not_found',
        	       			'content' => 'No menu found with this parameters'
           				);
	           			return response()->json($response, 404);
    	            }
            	} else {
               		$response = array(
                 		'status' => 'error',
                 		'message' => 'no_permission',
                 		'content' => 'You do not have permission to update this menu'
               		);
               		return response()->json($response);
            	}          
            } else {
            	$response = array(
	               			'status' => 'ok',
    	           			'message' => 'not_found',
        	       			'content' => 'No menu found with this parameters'
           		);
	           	return response()->json($response, 404);
            }	
        } catch(\Exception $e){
            $response = array(
               'status' => 'error',
               'message' => 'custom_error',
              'content' => $e
            );
           return response()->json($response);
        }    
    }

    public function show($venueId, $menuId){
        try {

			$menu = Menu::where('venue_id',$venueId)->get();

			if($menu){
				$response = array(
                	'status' => 'ok',
                	'message' => 'menu_details',
                	'content' => $menu->find($menuId)
            	);
            	return response()->json($response);	
			} else {
				$response = array(
                	'status' => 'ok',
                	'message' => 'not_found',
                	'content' => 'No menu found with this parameters'
            	);
            	return response()->json($response, 404);	
			}
        } catch(\Exception $e){
            $response = array(
               'status' => 'error',
               'message' => 'custom_error',
              'content' => $e
            );
           return response()->json($response);
        }   
    }

    public function destroy($venueId, $menuId){
        try {
            $venue = Venue::find($venueId);
            if($venue){
            	$users = $venue->users->where('id', Auth::user()->id)->first();
            	if(count($users) > 0){
            		$menu = Venue::where('id',$venueId)->whereHas('menus', function($q) use ($menuId)
            		{	
						$q->where('id', $menuId);
					})->first();
					if($menu){
        	        	$menu->delete();
            	    	$response = array(
                	    	'status' => 'ok',
                    		'message' => 'menu_deleted',
                    		'content' => $menu
                		);
                		return response()->json($response);
                	} else {
                		$response = array(
                			'status' => 'ok',
                			'message' => 'not_found',
                			'content' => 'No menu found with this parameters'
            			);
            			return response()->json($response, 404);		
                	}	
            	} else {
               		$response = array(
                 		'status' => 'error',
                 		'message' => 'no_permission',
                 		'content' => 'You do not have permission to delete this menu'
               		);
               		return response()->json($response);
            	} 
            } else {	
            	$response = array(
                	'status' => 'ok',
                	'message' => 'not_found',
                	'content' => 'No menu found with this parameters'
            	);
            	return response()->json($response, 404);	    
            }	

        } catch(\Exception $e){
            $response = array(
               'status' => 'error',
               'message' => 'custom_error',
              'content' => $e
            );
           return response()->json($response);
        }    
     }
}
