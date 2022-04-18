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

use think\Request;
use yunwuxin\social\AccessToken;
use yunwuxin\social\Channel;
use yunwuxin\social\exception\Exception;
use yunwuxin\social\User;

class Wechat extends Channel
{
    protected $baseUrl = 'https://api.weixin.qq.com/sns';

    protected $scopes = ['snsapi_login'];

    protected $unionid = false;

    public function __construct(Request $request, $config)
    {
        parent::__construct($request, $config);

        if (isset($config['unionid'])) {
            $this->unionid = $config['unionid'];
        }
    }

    protected function getAuthUrl()
    {
        $path = 'oauth2/authorize';
        if (in_array('snsapi_login', $this->scopes)) {
            $path = 'qrconnect';
        }
        return $this->buildAuthUrlFromBase("https://open.weixin.qq.com/connect/{$path}");
    }

    protected function getAuthParams()
    {
        return array_merge([
            'appid'         => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'response_type' => 'code',
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
        ], $this->parameters);
    }

    protected function buildAuthUrlFromBase($url)
    {
        $query = http_build_query($this->getAuthParams(), '', '&', $this->encodingType);
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

        if (isset($body['errcode'])) {
            throw new Exception($body['errmsg'], $body['errcode']);
        }

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
                'access_token' => $token->getValue(),
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
        if ($this->unionid) {
            $id = $this->unionid === true ? 'unionid' : $this->unionid;
        } else {
            $id = 'openid';
        }

        return User::make($user, [
            'id'     => $id,
            'name'   => 'nickname',
            'avatar' => 'headimgurl',
        ]);
    }
}
