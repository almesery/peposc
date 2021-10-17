<?php

namespace App\Http\Controllers;

use App\Helper\getLastLoginForEachUser;
use App\Models\LastLogin;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     * @throws Exception
     */
    public function index(Request $request, getLastLoginForEachUser $getLastLoginForEachUser)
    {
        if ($request->expectsJson()) {
            return $getLastLoginForEachUser->execute(Auth::user()->id);
        }
        return view('user.index');
    }

}
