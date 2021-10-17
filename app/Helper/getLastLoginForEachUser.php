<?php

namespace App\Helper;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;

class getLastLoginForEachUser
{

    /**
     * @return mixed
     * @throws Exception
     */
    public function execute($user_id)
    {
        $lastLogins = DB::table("last_logins")->where("user_id", $user_id)->select("*");
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
}
