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

class Social extends Manager
{
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

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return null;
    }
}
