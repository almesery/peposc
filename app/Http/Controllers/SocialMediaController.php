<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSocialMedia;
use Auth;
use DB;
use Socialite;

class SocialMediaController extends Controller
{
    public function providerRedirect($provider_type)
    {
        return Socialite::driver($provider_type)->redirect();
    }

    public function providerCallback($provider_type)
    {
        $socialiteUser = Socialite::driver($provider_type)->user();
        $social_media_user = DB::table("user_social_media")
            ->where('provider_type', $provider_type)
            ->where("email", $socialiteUser->getEmail())
            ->where("provider_id", $socialiteUser->getId());
        if ($social_media_user->exists()) {
            $user_id = $social_media_user->first()->user_id;
            Auth::guard()->loginUsingId($user_id);
            return redirect()->route("home");
        } else {
            tap(User::query()->create([
                "name" => $socialiteUser->getName(),
                "email" => $socialiteUser->getEmail(),
                "password" => \Hash::make(\Str::random(100)),
            ]), function (User $user) use ($provider_type, $socialiteUser) {
                $user->socialMediaProvider()->create([
                    "provider_type" => $provider_type,
                    "provider_id" => $socialiteUser->getId(),
                    "email" => $socialiteUser->getEmail(),
                ]);
            });
            return redirect()->route("home");
        }
    }
}
