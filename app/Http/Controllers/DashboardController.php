<?php

namespace App\Http\Controllers;

use App\Models\LastLogin;
use Auth;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class DashboardController extends Controller
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
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return datatables()
                ->of(Auth::user()->last_logins)
                ->addColumn('last_login_date', function (LastLogin $lastLogin) {
                    return $lastLogin->created_at->format('Y-m-d');
                })
                ->addColumn('last_login_time', function (LastLogin $lastLogin) {
                    return $lastLogin->created_at->format('H:i:s');
                })
                ->make(true);
        }
        return view('home');
    }
}
