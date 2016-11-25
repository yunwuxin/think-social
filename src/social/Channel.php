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
use think\helper\Str;
use think\Request;
use think\response\Redirect;
use think\Session;
use yunwuxin\exception\InvalidStateException;

abstract class Channel
{
    /** @var  Request */
    protected $request;

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

    protected $accessToken = null;

    public function __construct($config)
    {

        if (!isset($config['client_id']) || !isset($config['client_secret']) || !isset($config['redirect'])) {
            throw new \InvalidArgumentException("Config client_id,client_secret and redirect must be supply.");
        }

        $this->clientId     = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUrl  = $config['redirect'];
        $this->request      = Request::instance();
    }

    /**
     * 跳转到第三方平台登录
     */
    public function redirect()
    {
        $state = null;
        if ($this->usesState()) {
            Session::set('state', $state = $this->getState());
        }

        return new Redirect($this->getAuthUrl($state));
    }

    /**
     * Set redirect url.
     *
     * @param string $redirectUrl
     *
     * @return $this
     */
    public function withRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Return the redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * 获取第三方平台登录成功后的用户
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }
        $token = $this->getAccessToken($this->getCode());
        $user  = $this->makeUser($this->getUserByToken($token));
        return $user->setToken($token)->setChannel(strtolower(basename(str_replace('\\', '/', get_class($this)))));
    }

    /**
     * 设置scope
     *
     * @param  array $scopes
     * @return $this
     */
    public function scopes(array $scopes)
    {
        $this->scopes = array_unique(array_merge($this->scopes, $scopes));
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

    public function stateless()
    {
        $this->stateless = true;
        return $this;
    }

    protected function usesState()
    {
        return !$this->stateless;
    }

    protected function isStateless()
    {
        return $this->stateless;
    }

    protected function getState()
    {
        return Str::random(40);
    }

    protected function hasInvalidState()
    {
        if ($this->isStateless()) {
            return false;
        }
        $state = Session::pull('state');
        return !(strlen($state) > 0 && $this->request->param('state') === $state);
    }

    abstract protected function getAuthUrl($state);

    abstract protected function getTokenUrl();

    abstract protected function getUserByToken(AccessToken $token);

    /**
     * 创建User对象
     * @param array $user
     * @return User
     */
    abstract protected function makeUser(array $user);

    protected function getAuthParams($state)
    {
        $fields = array_merge([
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => $this->formatScopes($this->scopes, $this->scopeSeparator),
            'response_type' => 'code',
        ], $this->parameters);

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return $fields;
    }

    /**
     * 格式化scope
     * @param array  $scopes
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
     * @param $state
     * @return string
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        return $url . '?' . http_build_query($this->getAuthParams($state), '', '&', $this->encodingType);
    }

    /**
     * 获取返回的code
     */
    protected function getCode()
    {
        return $this->request->param('code');
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

    protected function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers'     => ['Accept' => 'application/json'],
            'form_params' => $this->getTokenParams($code),
        ]);

        $body = json_decode($response->getBody(), true);

        return AccessToken::make($body);
    }

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
