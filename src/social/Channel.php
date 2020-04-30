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
namespace yunwuxin\social;

use GuzzleHttp\Client;
use InvalidArgumentException;
use think\helper\Str;
use think\Request;
use yunwuxin\social\exception\InvalidStateException;

abstract class Channel
{
    protected $stateless = false;

    /** @var  Client Http 客户端 */
    protected $httpClient;

    protected $encodingType = PHP_QUERY_RFC1738;

    protected $clientId;
    protected $clientSecret;
    protected $redirectUrl = null;

    protected $scopes = [];

    /** @var string scope 分隔符 */
    protected $scopeSeparator = ',';

    /** @var array 自定义参数 */
    protected $parameters = [];

    /** @var Request */
    protected $request;

    public function __construct(Request $request, $config)
    {
        if (!isset($config['client_id']) || !isset($config['client_secret'])) {
            throw new InvalidArgumentException("Config client_id,client_secret must be supply.");
        }

        $this->clientId     = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->request      = $request;
    }

    /**
     * 跳转到第三方平台登录
     */
    public function redirect()
    {
        return redirect($this->getAuthUrl());
    }

    /**
     * Set redirect url.
     *
     * @param string $redirectUrl
     *
     * @return $this
     */
    public function setRedirectUrl(string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    protected function getCode()
    {
        return $this->request->param('code');
    }

    /**
     * 获取第三方平台登录成功后的用户
     * @param null|string $code
     * @return User
     */
    public function user($code = null)
    {
        if (is_null($code)) {
            $code = $this->getCode();
        }
        $accessToken = $this->getAccessToken($code);

        $user = $this->getUserByToken($accessToken);

        return $this->makeUser($user)
                    ->setToken($accessToken)
                    ->setChannel(strtolower(class_basename($this)));
    }

    /**
     * 设置scope
     *
     * @param array $scopes
     * @return $this
     */
    public function scopes(array $scopes)
    {
        $this->scopes = $scopes;
        return $this;
    }

    /**
     * 设置额外参数
     * @param array $parameters
     * @return $this
     */
    public function with(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * 获取scope
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    public function isStateless()
    {
        return $this->stateless;
    }

    abstract protected function getAuthUrl();

    abstract protected function getTokenUrl();

    abstract protected function getUserByToken(AccessToken $token);

    /**
     * 创建User对象
     * @param array $user
     * @return User
     */
    abstract protected function makeUser(array $user);

    protected function getAuthParams()
    {
        return array_merge([
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'response_type' => 'code',
        ], $this->parameters);
    }

    /**
     * 格式化scope
     * @param array $scopes
     * @param string $scopeSeparator
     * @return string
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }

    /**
     * 创建认证跳转url
     * @param $url
     * @return string
     */
    protected function buildAuthUrlFromBase($url)
    {
        return $url . '?' . http_build_query($this->getAuthParams(), '', '&', $this->encodingType);
    }

    protected function getTokenParams($code)
    {
        return [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'redirect_uri'  => $this->redirectUrl,
        ];
    }

    abstract protected function getAccessToken($code);

    /**
     * 获取http客户端实例
     * @return Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client();
        }
        return $this->httpClient;
    }
}
