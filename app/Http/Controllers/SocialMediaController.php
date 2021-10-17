<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSocialMedia;
use Auth;
use DB;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;
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
                Auth::loginUsingId($user->id);
            });
            return redirect()->route("home");
        }
    }

    public function signin()
    {
        // Initialize the OAuth client
        $oauthClient = new GenericProvider([
            'clientId' => config('services.hotmail.appId'),
            'clientSecret' => config('services.hotmail.appSecret'),
            'redirectUri' => config('services.hotmail.redirectUri'),
            'urlAuthorize' => config('services.hotmail.authority') . config('services.hotmail.authorizeEndpoint'),
            'urlAccessToken' => config('services.hotmail.authority') . config('services.hotmail.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes' => config('services.hotmail.scopes')
        ]);

        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        session(['oauthState' => $oauthClient->getState()]);

        // Redirect to AAD signin page
        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        // Validate state
        $expectedState = session('oauthState');
        $request->session()->forget('oauthState');
        $providedState = $request->query('state');


        if (!isset($expectedState)) {
            return redirect('/');
        }

        if (!isset($providedState) || $expectedState != $providedState) {
            return redirect('/')
                ->with('error', 'Invalid auth state')
                ->with('errorDetail', 'The provided auth state did not match the expected value');
        }

        // Authorization code should be in the "code" query param
        $authCode = $request->query('code');
        if (isset($authCode)) {
            // Initialize the OAuth client
            $oauthClient = new GenericProvider([
                'clientId' => config('services.hotmail.appId'),
                'clientSecret' => config('services.hotmail.appSecret'),
                'redirectUri' => config('services.hotmail.redirectUri'),
                'urlAuthorize' => config('services.hotmail.authority') . config('services.hotmail.authorizeEndpoint'),
                'urlAccessToken' => config('services.hotmail.authority') . config('services.hotmail.tokenEndpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes' => config('services.hotmail.scopes')
            ]);

            try {
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $authCode
                ]);

                // TEMPORARY FOR TESTING!
                return redirect('/')
                    ->with('error', 'Access token received')
                    ->with('errorDetail', $accessToken->getToken());
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return redirect('/')
                    ->with('error', 'Error requesting access token')
                    ->with('errorDetail', json_encode($e->getResponseBody()));
            }
        }

        return redirect('/')
            ->with('error', $request->query('error'))
            ->with('errorDetail', $request->query('error_description'));
    }
}
