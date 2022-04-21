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

/**
 * Class Social
 * @package yunwuxin
 * @mixin Github
 */
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

    protected function resolveParams($name): array
    {
        return array_merge([$name], parent::resolveParams($name));
    }

    protected function createDriver(string $name)
    {
        /** @var Channel $channel */
        $channel = parent::createDriver($name);

        $redirectUrl = url('SOCIAL_CALLBACK', ['channel' => $name])->domain(true);
        $channel->setRedirectUrl($redirectUrl);
        return $channel;
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
