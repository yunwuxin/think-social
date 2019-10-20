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

use GuzzleHttp\Exception\ClientException;
use yunwuxin\social\AccessToken;
use yunwuxin\social\Channel;
use yunwuxin\social\exception\Exception;
use yunwuxin\social\User;

class Weibo extends Channel
{

    protected $baseUrl = "https://api.weibo.com";

    protected $version = '2';

    protected $scopes = ['email'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/oauth2/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl . '/oauth2/access_token';
    }

    protected function getTokenParams($code)
    {
        return parent::getTokenParams($code) + ['grant_type' => 'authorization_code'];
    }

    protected function getAccessToken($code)
    {
        try {
            $response = $this->getHttpClient()->post($this->getTokenUrl(), [
                'headers'     => ['Accept' => 'application/json'],
                'form_params' => $this->getTokenParams($code),
            ]);

            $body = json_decode($response->getBody(), true);

            return AccessToken::make($body);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            throw new Exception($body['error'], $body['error_code']);
        }
    }

    protected function getUserByToken(AccessToken $token)
    {
        $response = $this->getHttpClient()->get($this->baseUrl . '/' . $this->version . '/users/show.json', [
            'query'   => [
                'uid'          => $token->getRaw('uid'),
                'access_token' => $token->getValue(),
            ],
            'headers' => [
                'Accept' => 'application/json',
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
            'nickname' => 'screen_name',
            'avatar'   => 'avatar_large',
        ]);
    }
}
