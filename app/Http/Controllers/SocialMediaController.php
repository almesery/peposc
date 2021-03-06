<?php

namespace App\Http\Controllers;

use App\Actions\User\storeUserLastLoginAction;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\RedirectResponse;
use Socialite;

class SocialMediaController extends Controller
{
    /**
     * @param $provider_type
     * @return mixed
     */
    public function providerRedirect($provider_type)
    {
        return Socialite::driver($provider_type)->redirect();
    }

    /**
     * @param $provider_type
     * @return RedirectResponse
     */
    public function providerCallback($provider_type): RedirectResponse
    {
        $socialiteUser = Socialite::driver($provider_type)->user();
        $social_media_user = DB::table("user_social_media")
            ->where('provider_type', $provider_type)
            ->where("email", $socialiteUser->getEmail())
            ->where("provider_id", $socialiteUser->getId());
        if ($social_media_user->exists()) {
            $user_id = $social_media_user->first()->user_id;
            Auth::guard()->loginUsingId($user_id);
        } else {
            tap(User::query()->create([
                "name" => $socialiteUser->getName(),
                "email" => $socialiteUser->getEmail(),
                "password" => \Hash::make(\Str::random(100)),
                "email_verified_at" => Carbon::now(),
            ]), function (User $user) use ($provider_type, $socialiteUser) {
                $user->socialMediaProvider()->create([
                    "provider_type" => $provider_type,
                    "provider_id" => $socialiteUser->getId(),
                    "email" => $socialiteUser->getEmail(),
                ]);
                Auth::loginUsingId($user->id);
            });
        }
        storeUserLastLoginAction::execute();
        return redirect()->route("home");
    }
}
