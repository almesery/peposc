<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSocialMedia;
use App\TokenStore\TokenCache;
use Auth;
use DB;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Graph;
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
        $oauthClient = new GenericProvider([
            'clientId' => config('azure.appId'),
            'clientSecret' => config('azure.appSecret'),
            'redirectUri' => config('azure.redirectUri'),
            'urlAuthorize' => config('azure.authority') . config('azure.authorizeEndpoint'),
            'urlAccessToken' => config('azure.authority') . config('azure.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes' => config('azure.scopes')
        ]);
        $authUrl = $oauthClient->getAuthorizationUrl();
        session(['oauthState' => $oauthClient->getState()]);
        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
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
        $authCode = $request->query('code');
        if (isset($authCode)) {
            // Initialize the OAuth client
            $oauthClient = new GenericProvider([
                'clientId' => config('azure.appId'),
                'clientSecret' => config('azure.appSecret'),
                'redirectUri' => config('azure.redirectUri'),
                'urlAuthorize' => config('azure.authority') . config('azure.authorizeEndpoint'),
                'urlAccessToken' => config('azure.authority') . config('azure.tokenEndpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes' => config('azure.scopes')
            ]);
//            try {
                $accessToken = $oauthClient->getAccessToken('authorization_code', ['code' => $authCode]);
                dd($accessToken);
                $graph = new Graph();
                $graph->setAccessToken($accessToken->getToken());
                $user = $graph->createRequest('GET', '/me?$select=displayName,mail,mailboxSettings,userPrincipalName')->setReturnType(User::class)->execute();
                $tokenCache = new TokenCache();
                $tokenCache->storeTokens($accessToken, $user);
//            } catch (IdentityProviderException $e) {
//                return redirect('/')
//                    ->with('error', 'Error requesting access token')
//                    ->with('errorDetail', json_encode($e->getResponseBody()));
//            }
        }
        return redirect('/');
    }

    public function signout()
    {
        $tokenCache = new TokenCache();
        $tokenCache->clearTokens();
        return redirect('/');
    }
}
