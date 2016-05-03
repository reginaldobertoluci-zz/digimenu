<?php
namespace App\Http\Controllers;
use App\Permission;
use App\Role;
use App\User;
use App\Menu;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;

class MenuController extends Controller
{
    
    public function index()
    {
        return response()->json(['auth'=>Auth::user(), 'menus'=>Menu::all()]);
    }

    public function getOne($id)
    {

		$menu = Menu::with(['items', 'categories'])->findOrFail($id);
		$response = array(
			'auth' => Auth::user(),
			'menu' => $menu
		);

        return response()->json($response);
    }

}    