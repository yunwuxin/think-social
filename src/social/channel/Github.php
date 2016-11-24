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

class Github extends Channel
{
    protected $scopes = ['user:email'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://github.com/login/oauth/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://github.com/login/oauth/access_token';
    }

    protected function getUserByToken(AccessToken $token)
    {
        $userUrl  = 'https://api.github.com/user?access_token=' . $token->getToken();
        $response = $this->getHttpClient()->get(
            $userUrl, $this->getRequestOptions()
        );
        $user     = json_decode($response->getBody(), true);
        if (in_array('user:email', $this->scopes)) {
            $user['email'] = $this->getEmailByToken($token->getToken());
        }
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
            'nickname' => 'login',
            'avatar'   => 'avatar_url'
        ]);
    }

    /**
     * 获取email
     * @param $token|null
     */
    protected function getEmailByToken($token)
    {
        $emailsUrl = 'https://api.github.com/user/emails?access_token=' . $token;
        try {
            $response = $this->getHttpClient()->get(
                $emailsUrl, $this->getRequestOptions()
            );
        } catch (\Exception $e) {
            return;
        }
        foreach (json_decode($response->getBody(), true) as $email) {
            if ($email['primary'] && $email['verified']) {
                return $email['email'];
            }
        }
    }

    /**
     * 设置http请求参数
     *
     * @return array
     */
    protected function getRequestOptions()
    {
        return [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ];
    }

}