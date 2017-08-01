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
use yunwuxin\social\exception\Exception;
use yunwuxin\social\User;

class Qq extends Channel
{

    /**
     * qq登录接口地址
     * @var string
     */
    protected $baseUrl = "https://graph.qq.com";

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->baseUrl . '/oauth2.0/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return $this->baseUrl . '/oauth2.0/token';
    }

    protected function getTokenParams($code)
    {
        return parent::getTokenParams($code) + ['grant_type' => 'authorization_code'];
    }

    protected function getAccessToken($code)
    {
        $response = $this->getHttpClient()->get($this->getTokenUrl(), [
            'query' => $this->getTokenParams($code),
        ]);

        $content = $response->getBody()->getContents();

        parse_str($content, $token);

        if (isset($token['access_token'])) {
            return AccessToken::make($token);
        } else {
            $result = json_decode($this->removeCallback($content), true);
            throw new Exception($result['error_description'], $result['error']);
        }
    }

    protected function getUserByToken(AccessToken $token)
    {
        $url = $this->baseUrl . '/oauth2.0/me?access_token=' . $token->getToken();

        $response = $this->getHttpClient()->get($url);
        $me       = json_decode($this->removeCallback($response->getBody()->getContents()), true);

        $openId = $me['openid'];

        $queries    = [
            'access_token'       => $token->getToken(),
            'openid'             => $openId,
            'oauth_consumer_key' => $this->clientId,
        ];
        $response   = $this->getHttpClient()->get($this->baseUrl . '/user/get_user_info?' . http_build_query($queries));
        $user       = json_decode($this->removeCallback($response->getBody()->getContents()), true);
        $user['id'] = $openId;

        return $user;
    }

    /**
     * 创建User对象
     * @param array $user
     * @return User
     */
    protected function makeUser(array $user)
    {
        return User::make($user, [
            'name'   => 'nickname',
            'avatar' => 'figureurl_qq_2'
        ]);
    }

    protected function removeCallback($response)
    {
        if (strpos($response, 'callback') !== false) {
            $lpos     = strpos($response, '(');
            $rpos     = strrpos($response, ')');
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        return $response;
    }
}