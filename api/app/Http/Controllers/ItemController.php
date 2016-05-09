<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Venue;
use App\Menu;
use App\Item;

class ItemController extends Controller
{
    public function index($venueId, $menuId, $sectionId)
    {
    	try {
    		
			$items = Menu::where('venue_id', $venueId)->find($menuId)->items()->where('section_id', $sectionId)->get();

			if($items){
				$response = array(
                  'status' => 'ok',
                  'message' => 'get_items',
                  'content' => $items
            	);
            	return response()->json($response);
   			} else {
    			$response = array(
	          			'status' => 'error',
    	       			'message' => 'not_found',
        	  			'content' => 'No items found with this parameters'
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
        return response()->json(['items'=>Item::all()]);
    }

    public function store(Request $request, $venueId, $menuId, $sectionId){
      try {
        $venue = Venue::find($venueId);
        if($venue){
			//Verifica se o usuário tem permissão para acessar este estabelecimento
			$users = $venue->users->where('id', Auth::user()->id)->first();
            if(count($users) > 0){
			   $section = Menu::where('venue_id', $venueId)->find($menuId)->sections()->where('id', $sectionId)->first();

			   if($section){
    	      		$item = $request->all();
        			$v = Validator::make($item, [
              			 'name' => 'required|max:255',
              		 	'price' =>'required'
        			]);
        		 
		        	if($v->passes()){
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
        	       			'content' => 'No section found with this parameters'
           			);
	           		return response()->json($response, 404);	
              }  
            // Se o usuário não tem permissão para acessar este estabelecimento...  
            } else {
                $response = array(
                  'status' => 'error',
                  'message' => 'no_permission',
                  'content' => 'You do not have permission to create items on this section'
                );
                return response()->json($response);
             } 
          } else {
				$response = array(
	               			'status' => 'ok',
    	           			'message' => 'not_found',
        	       			'content' => 'No section found with this parameters'
           		);
	           	return response()->json($response, 404);	
          }    
       } catch(\Exception $e){
             $response = array(
                  'status' => 'error',
                  'message' => 'custom_error',
                  'content' => $e->getMessage()
             );
             return response()->json($response);
       } 
        
    }    

    public function update(Request $request, $venueId, $menuId, $sectionId, $itemId)
    {
        $data = $request->all();
        try {
            $venue = Venue::find($venueId);
            if($venue){
            	$users = $venue->users->where('id', Auth::user()->id)->first();
            	if(count($users) > 0){
					$item = Menu::where('venue_id', $venueId)->find($menuId)->items()->where('section_id', $sectionId)->where('menu_items.id', $itemId)->first();

					if($item){
                		$v = Validator::make($data, [
                    		'name' => 'required|max:255',
                    		'price' =>'required'
                		]);
                		if($v->passes()){
                   			$item->update($data); 
                   			$response = array(
	                      		'status' => 'ok',
    	                  		'message' => 'item_updated',
        	              		'content' => $data
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
        	       			'content' => 'No item found with this parameters'
           				);
	           			return response()->json($response, 404);
                	}	
            	} else {
               		$response = array(
                 		'status' => 'error',
                 		'message' => 'no_permission',
                 		'content' => 'You do not have permission to update this item'
               		);
               		return response()->json($response);
            	}          
            } else {
            	$response = array(
	               			'status' => 'ok',
    	           			'message' => 'not_found',
        	       			'content' => 'No item found with this parameters'
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

    public function show($venueId, $menuId, $sectionId, $itemId){
        try {

			$item = Menu::where('venue_id', $venueId)->find($menuId)->items()->where('section_id', $sectionId)->where('menu_items.id', $itemId)->first();

			if($item){
				$response = array(
                	'status' => 'ok',
                	'message' => 'item_details',
                	'content' => $item
            	);
            	return response()->json($response);	
			} else {
				$response = array(
                	'status' => 'ok',
                	'message' => 'not_found',
                	'content' => 'No item found with this parameters'
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

    public function destroy($venueId, $menuId, $sectionId){
        try {
            $venue = Venue::find($venueId);
            if($venue){
            	$users = $venue->users->where('id', Auth::user()->id)->first();
            	if(count($users) > 0){
					$item = Menu::where('venue_id', $venueId)->find($menuId)->items()->where('section_id', $sectionId)->where('menu_items.id', $itemId)->first();

					if($item){
        	        	$item->delete();
            	    	$response = array(
                	    	'status' => 'ok',
                    		'message' => 'item_deleted',
                    		'content' => $item
	                	);
    	            	return response()->json($response);
    	            } else {	
    	            	$response = array(
                			'status' => 'ok',
                			'message' => 'not_found',
                			'content' => 'No item found with this parameters'
            			);
            			return response()->json($response, 404);		
            		}	
            	} else {
               		$response = array(
                 		'status' => 'error',
                 		'message' => 'no_permission',
                 		'content' => 'You do not have permission to delete this item'
               		);
               		return response()->json($response);
            	}  
            } else {
            	$response = array(
	               			'status' => 'ok',
    	           			'message' => 'not_found',
        	       			'content' => 'No item found with this parameters'
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
