<?php

namespace App\Http\Controllers;

use App\Models\LastLogin;
use Auth;
use Carbon\Carbon;
use DB;
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
            $lastLogins = DB::table("last_logins")->where("user_id", Auth::id())->select("*");
            return datatables()
                ->of($lastLogins)
                ->addColumn('last_login_date', function ($lastLogin) {
                    return Carbon::parse($lastLogin->created_at)->format('Y-m-d');
                })
                ->addColumn('last_login_time', function ($lastLogin) {
                    return Carbon::parse($lastLogin->created_at)->format('H:i:s');
                })
                ->make(true);
        }
        return view('home');
    }
}
