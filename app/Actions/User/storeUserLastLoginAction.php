<?php

namespace App\Actions\User;

use App\Models\LastLogin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class storeUserLastLoginAction
{
    /**
     * @return Builder|Model
     */
    public static function execute()
    {
        return LastLogin::query()->create([
            "user_id" => request()->user()->id,
            "ip_address" => request()->ip()
        ]);
    }
}
