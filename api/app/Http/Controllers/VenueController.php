<?php
namespace App\Http\Controllers;
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