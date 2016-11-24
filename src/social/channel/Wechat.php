<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace yunwuxin\social\channel;

use yunwuxin\social\AccessToken;
use yunwuxin\social\Channel;
use yunwuxin\social\User;

class Wechat extends Channel
{
    protected $baseUrl = 'https://api.weixin.qq.com/sns';

    protected $scopes = ['snsapi_login'];

    protected $stateless = true;

    protected function getAuthUrl($state)
    {
        $path = 'oauth2/authorize';
        if (in_array('snsapi_login', $this->scopes)) {
            $path = 'qrconnect';
        }
        return $this->buildAuthUrlFromBase("https://open.weixin.qq.com/connect/{$path}", $state);
    }

    protected function getAuthParams($state = null)
    {
        return array_merge([
            'appid'         => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'response_type' => 'code',
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'state'         => $state ?: md5(time()),
        ], $this->parameters);
    }

    protected function buildAuthUrlFromBase($url, $state)
    {
        $query = http_build_query($this->getAuthParams($state), '', '&', $this->encodingType);
        return $url . '?' . $query . '#wechat_redirect';
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl . '/oauth2/access_token';
    }

    protected function getTokenParams($code)
    {
        return [
            'appid'      => $this->clientId,
            'secret'     => $this->clientSecret,
            'code'       => $code,
            'grant_type' => 'authorization_code',
        ];
    }

    protected function getAccessToken($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenParams($code),
        ]);
        $body     = json_decode($response->getBody()->getContents(), true);
        return AccessToken::make($body);
    }

    protected function getUserByToken(AccessToken $token)
    {
        $scopes = explode(',', $token->getRaw('scope', ''));
        if (in_array('snsapi_base', $scopes)) {
            return $token->getRaw();
        }
        if (empty($token->getRaw('openid'))) {
            throw new \InvalidArgumentException('openid of AccessToken is required.');
        }

        $response = $this->getHttpClient()->get($this->baseUrl . '/userinfo', [
            'query' => [
                'access_token' => $token->getToken(),
                'openid'       => $token->getRaw('openid'),
                'lang'         => 'zh_CN',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    /**
     * 创建User对象
     * @param array $user
     * @return User
     */
    protected function makeUser(array $user)
    {
        return User::make($user, [
            'id'     => 'openid',
            'name'   => 'nickname',
            'avatar' => 'headimgurl'
        ]);
    }
}