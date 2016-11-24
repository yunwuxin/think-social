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
use think\Config;
use yunwuxin\social\Channel;

class Social
{
    /** @var Channel[] */
    protected static $channels = [];

    /**
     * 获取一个社会化渠道
     * @param string $name
     * @return mixed
     */
    public static function channel($name)
    {
        $name = strtolower($name);
        if (!isset(self::$channels[$name])) {
            self::$channels[$name] = self::buildChannel($name);
        }

        return self::$channels[$name];
    }

    /**
     * 创建渠道
     * @param string $name
     * @return Channel
     */
    protected static function buildChannel($name)
    {
        $className = "\\yunwuxin\\social\\channel\\" . ucfirst($name);
        if (class_exists($className)) {
            $config = Config::get('social.' . $name);

            return new $className($config);
        }
        throw new InvalidArgumentException("Channel [{$name}] not supported.");
    }

}