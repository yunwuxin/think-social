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

    protected function resolveType(string $name)
    {
        return $this->app->config->get("social.channels.{$name}.type") ?? $name;
    }

    protected function resolveConfig(string $name)
    {
        return $this->app->config->get("social.channels.{$name}", []);
    }

    public function checkUser(User $user, $autoLogin = true): bool
    {
        $checker = $this->app->config->get('social.user_checker');

        return $this->app->invoke($checker, [$user, $autoLogin]);
    }

    public function setFlashUser(User $user)
    {
        $this->app->session->flash(self::USER_NAME, $this->user());
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
