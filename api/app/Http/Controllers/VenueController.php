<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Venue;
use App\User;

class VenueController extends Controller
{
    public function index()
    {
        try {
            $venues = Venue::all();
            $response = array(
                  'status' => 'ok',
                  'message' => 'get_venues',
                  'content' => $venues
            );
            return response()->json($response);
        } catch(\Exception $e){
            $response = array(
                    'status' => 'error',
                    'message' => 'custom_error',
                    'content' => $e->getMessage()
            );
            return response()->json($response, 500);

        }    
    }

    public function store(Request $request)
    {
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

    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            $venue = Venue::find($id);
            $users = $venue->users->where('id', Auth::user()->id)->first();
            if(count($users) > 0){
                $v = Validator::make($data, [
                    'name' => 'required|max:255'
                ]);
                if($v->passes()){
                   $venue->update($data); 
                   $response = array(
                      'status' => 'ok',
                      'message' => 'venue_updated',
                      'content' => $venue
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
                 'status' => 'error',
                 'message' => 'no_permission',
                 'content' => 'You do not have permission to update this venue'
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
    }

    public function show($id){
        try {
            $venue = Venue::findOrFail($id);
            $response = array(
                'status' => 'ok',
                'message' => 'venue_details',
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
    }

     public function destroy($id){
        try {
            $venue = Venue::findOrFail($id);
            $users = $venue->users->where('id', Auth::user()->id)->first();
            if(count($users) > 0){
                $venue->delete();
                $response = array(
                    'status' => 'ok',
                    'message' => 'venue_deleted',
                    'content' => $venue
                );
                return response()->json($response);
            } else {
               $response = array(
                 'status' => 'error',
                 'message' => 'no_permission',
                 'content' => 'You do not have permission to delete this venue'
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
     }
}
