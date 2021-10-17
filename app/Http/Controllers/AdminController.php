<?php

namespace App\Http\Controllers;

use App\Helper\getLastLoginForEachUser;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Log;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $users = DB::table("users")
                ->where("users.id", "!=", 1)
                ->select(
                    "users.*",
                    DB::raw("(SELECT created_at FROM last_logins WHERE last_logins.user_id = users.id ORDER BY id desc limit 1) as userLastLogin")
                );
            return datatables()
                ->of($users)
                ->addColumn("last_login_date", function ($user) {
                    return $user->userLastLogin;
                })
                ->addColumn("actions", function ($user) {
                    return view("admin.actions")->with([
                        "user" => $user
                    ]);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view("admin.index");
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param User $user
     * @param getLastLoginForEachUser $getLastLoginForEachUser
     * @return Application|Factory|View|Response
     * @throws Exception
     */
    public function show(Request $request, User $user, getLastLoginForEachUser $getLastLoginForEachUser)
    {
        if ($request->expectsJson()) {
            return $getLastLoginForEachUser->execute($user->id);
        }
        return \view("admin.show")->with([
            "user" => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return \response()->json([
            "success" => true,
            "message" => "User Deleted Successfully"
        ]);
    }
}
