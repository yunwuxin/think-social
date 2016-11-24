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

class Qq extends Channel
{

    /**
     * qq登录接口地址
     * @var string
     */
    protected $baseUrl = "https://graph.qq.com";

    protected $withUnionId = false;

    public function __construct($config)
    {
        parent::__construct($config);
        if (isset($config['union']) && $config['union']) {
            $this->withUnionId = true;
        }
    }

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
        return AccessToken::make($token);
    }

    protected function getUserByToken(AccessToken $token)
    {
        $url = $this->baseUrl . '/oauth2.0/me?access_token=' . $token->getToken();
        $this->withUnionId && $url .= '&unionid=1';
        $response = $this->getHttpClient()->get($url);
        $me       = json_decode($this->removeCallback($response->getBody()->getContents()), true);

        $openId  = $me['openid'];
        $unionId = isset($me['unionid']) ? $me['unionid'] : '';

        $queries          = [
            'access_token'       => $token->getToken(),
            'openid'             => $openId,
            'oauth_consumer_key' => $this->clientId,
        ];
        $response         = $this->getHttpClient()->get($this->baseUrl . '/user/get_user_info?' . http_build_query($queries));
        $user             = json_decode($this->removeCallback($response->getBody()->getContents()), true);
        $user['id']       = $openId;
        $user['union_id'] = $unionId;

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