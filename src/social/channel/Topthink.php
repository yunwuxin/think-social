<?php

namespace yunwuxin\social\channel;

use yunwuxin\social\AccessToken;
use yunwuxin\social\Channel;
use yunwuxin\social\exception\Exception;
use yunwuxin\social\User;

class Topthink extends Channel
{
    protected $host = "https://www.topthink.com";

    protected $implicit = false;

    protected function initialize($config)
    {
        if (!empty($config['host'])) {
            $this->host = rtrim($config['host'], '/');
        }

        if (isset($config['implicit'])) {
            $this->implicit = $config['implicit'];
        }
    }

    public function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase("{$this->host}/oauth/authorize");
    }

    protected function getAuthParams()
    {
        return array_merge([
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'response_type' => $this->implicit ? 'token' : 'code',
        ], $this->parameters);
    }

    protected function getTokenUrl()
    {
        return "{$this->host}/oauth/token";
    }

    protected function getUserByToken(AccessToken $token)
    {
        $userUrl  = "{$this->host}/api/me";
        $response = $this->getHttpClient()->get($userUrl, [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => "Bearer {$token}",
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function makeUser(array $user)
    {
        return User::make($user, [
            'nickname' => 'name',
        ]);
    }

    protected function getTokenParams($code)
    {
        return parent::getTokenParams($code) + ['grant_type' => 'authorization_code'];
    }

    protected function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers'     => ['Accept' => 'application/json'],
            'form_params' => $this->getTokenParams($code),
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['access_token'])) {
            return AccessToken::make($body);
        }

        throw new Exception();
    }
}
