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
namespace yunwuxin;

use InvalidArgumentException;
use think\helper\Arr;
use think\Manager;
use yunwuxin\social\Channel;
use yunwuxin\social\channel\Github;
use yunwuxin\social\User;

/**
 * Class Social
 * @package yunwuxin
 * @mixin Github
 */
class Social extends Manager
{
    const USER_NAME = 'social_user';

    protected $namespace = '\\yunwuxin\\social\\channel\\';

    /**
     * 获取一个社会化渠道
     * @param string $name
     * @return Channel
     */
    public function channel($name)
    {
        return $this->driver($name);
    }

    /**
     * 获取配置
     * @param null|string $name 名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, $default = null)
    {
        if (!is_null($name)) {
            return $this->app->config->get('social.' . $name, $default);
        }

        return $this->app->config->get('social');
    }

    /**
     * 获取驱动配置
     * @param string $channel
     * @param ?string $name
     * @param null $default
     * @return array
     */
    public function getChannelConfig(string $channel, string $name = null, $default = null)
    {
        if ($config = $this->getConfig("channels.{$channel}")) {
            return Arr::get($config, $name, $default);
        }

        throw new InvalidArgumentException("Channel [$channel] not found.");
    }

    protected function resolveType(string $name)
    {
        return $this->getChannelConfig($name, 'type', $name);
    }

    protected function resolveConfig(string $name)
    {
        return $this->getChannelConfig($name);
    }

    public function checkUser(User $user, $autoLogin = true): bool
    {
        $checker = $this->app->config->get('social.user_checker');
        if ($checker) {
            return $this->app->invoke($checker, [$user, $autoLogin]);
        }
        return false;
    }

    public function setFlashUser(User $user)
    {
        $this->app->session->flash(self::USER_NAME, $user);
    }

    public function getFlashUser(): User
    {
        return $this->app->session->get(self::USER_NAME);
    }

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return null;
    }
}
