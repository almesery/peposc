<?php

namespace App\TokenStore;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class TokenCache
{
    public function storeTokens($accessToken, $user)
    {
        session([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires(),
            'userName' => $user->getDisplayName(),
            'userEmail' => null !== $user->getMail() ? $user->getMail() : $user->getUserPrincipalName(),
            'userTimeZone' => $user->getMailboxSettings()->getTimeZone()
        ]);
    }

    public function clearTokens()
    {
        session()->forget('accessToken');
        session()->forget('refreshToken');
        session()->forget('tokenExpires');
        session()->forget('userName');
        session()->forget('userEmail');
        session()->forget('userTimeZone');
    }

    public function getAccessToken()
    {
        if (empty(session('accessToken')) ||
            empty(session('refreshToken')) ||
            empty(session('tokenExpires'))) {
            return '';
        }
        $now = time() + 300;
        if (session('tokenExpires') <= $now) {
            $oauthClient = new GenericProvider([
                'clientId' => config('azure.appId'),
                'clientSecret' => config('azure.appSecret'),
                'redirectUri' => config('azure.redirectUri'),
                'urlAuthorize' => config('azure.authority') . config('azure.authorizeEndpoint'),
                'urlAccessToken' => config('azure.authority') . config('azure.tokenEndpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes' => config('azure.scopes')
            ]);
            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => session('refreshToken')
                ]);
                $this->updateTokens($newToken);
                return $newToken->getToken();
            } catch (IdentityProviderException $e) {
                return '';
            }
        }
        return session('accessToken');
    }

    public function updateTokens($accessToken)
    {
        session([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires()
        ]);
    }
}
