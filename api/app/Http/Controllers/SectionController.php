<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Venue;
use App\Menu;
use App\Section;
use App\User;
use DB;

class SectionController extends Controller
{
    public function index($venueId, $menuId)
    {
    	try {
			$sections = Venue::find($venueId)->sections()->where('menu_id', $menuId)->get();
    		if($sections){
  				$response = array(
               		'status' => 'ok',
              		'message' => 'get_sections',
             		'content' => $sections
          		);
           		return response()->json($response);
   			} else {
   				$response = array(
               			'status' => 'error',
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
        	return response()->json($response, 500);
    	}	
        
    }

    public function store(Request $request, $venueId, $menuId){
      try {
        $venue = Venue::find($venueId);
        if($venue){
			//Verifica se o usuário tem permissão para acessar este estabelecimento
			$users = $venue->users->where('id', Auth::user()->id)->first();
            if(count($users) > 0){
              $menu = Venue::where('id',$venueId)->whereHas('menus', function($q) use ($menuId)
			  {
				$q->where('id', $menuId);
			  })->first();
    	      if($menu){
    	      	$section = $request->all();
        		$v = Validator::make($section, [
              		'name' => 'required|max:255',
              		'order' =>'required'
        		]);
       
		        if($v->passes()){
	                $section = Section::create($section);
    	            // Associa a seção ao menu
        	        $section->menu()->associate($menu);
            	    $section->save();   

                	$response = array(
                    	'status' => 'ok',
                    	'message' => 'section_created',
                    	'content' => ['id' => $section->id, 'name' => $section->name, 'order' => $section->order]
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
                  'content' => 'You do not have permission to create sections on this menu'
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

    public function update(Request $request, $venueId, $menuId, $sectionId)
    {
        $data = $request->all();
        try {
            $venue = Venue::find($venueId);
            if($venue){
            	$users = $venue->users->where('id', Auth::user()->id)->first();
            	if(count($users) > 0){
					$section = Venue::find($venueId)->sections()->where('menu_id', $menuId)->where('sections.id', $sectionId)->first();

            		/*
					$section = Venue::where('id',$venueId)->whereHas('menus', function($q) use ($menuId, $sectionId)
					{
						$q->where('id', $menuId)->whereHas('sections', function($q) use ($sectionId)
    					{
        					$q->where('id', $sectionId);
    					});
					})->first();
            		*/

					if($section){
                		$v = Validator::make($data, [
                    		'name' => 'required|max:255',
                    		'order' =>'required'
                		]);
                		if($v->passes()){
                   			$section->update($data); 
                   			$response = array(
	                      		'status' => 'ok',
    	                  		'message' => 'section_updated',
        	              		'content' => $section
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
            	} else {
               		$response = array(
                 		'status' => 'error',
                 		'message' => 'no_permission',
                 		'content' => 'You do not have permission to update this section'
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
              'content' => $e
            );
           return response()->json($response);
        }    
    }

    public function show($venueId, $menuId, $sectionId){

		try {

			$section = Venue::find($venueId)->sections()->where('menu_id', $menuId)->where('sections.id', $sectionId)->first();

			if($section){
				$response = array(
                	'status' => 'ok',
                	'message' => 'section_details',
                	'content' => $section
            	);
            	return response()->json($response);	
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

    public function destroy($venueId, $menuId, $sectionId){
        try {
            $venue = Venue::find($venueId);
            if($venue){
            	$users = $venue->users->where('id', Auth::user()->id)->first();
            	if(count($users) > 0){
					$section = Venue::find($venueId)->sections()->where('menu_id', $menuId)->where('sections.id', $sectionId)->first();

					/*$section = Venue::where('id',$venueId)->whereHas('menus', function($q) use ($menuId, $sectionId){
						$q->where('id', $menuId)->whereHas('sections', function($q) use ($sectionId)
    					{
        					$q->where('id', $sectionId);
    					});
					})->first();
					*/
					if($section){
        	        	$section->delete();
            	    	$response = array(
                	    	'status' => 'ok',
                    		'message' => 'section_deleted',
                    		'content' => $section
	                	);
    	            	return response()->json($response);
    	            } else {	
    	            	$response = array(
                			'status' => 'ok',
                			'message' => 'not_found',
                			'content' => 'No section found with this parameters'
            			);
            			return response()->json($response, 404);		
            		}	
            	} else {
               		$response = array(
                 		'status' => 'error',
                 		'message' => 'no_permission',
                 		'content' => 'You do not have permission to delete this section'
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
              'content' => $e
            );
           return response()->json($response);
        }    
     }
}
