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

use ArrayAccess;

class User implements ArrayAccess
{
    protected $raw;

    protected $user;

    protected function __construct($user, $raw)
    {
        $this->user = $user;
        $this->raw  = $raw;
    }

    public static function make($raw, $map)
    {
        $fields = ['id', 'nickname', 'email', 'avatar'];
        $user   = [];
        array_map(function ($field) use ($raw, $map, &$user) {
            $key = $field;
            if (isset($map[$field])) {
                $key = $map[$field];
            }
            $user[$field] = isset($raw[$key]) ? $raw[$key] : null;
        }, $fields);
        return new self($user, $raw);
    }

    public function setToken(AccessToken $token)
    {
        $this->user['token'] = $token;
        return $this;
    }

    public function setChannel($channel)
    {
        $this->user['channel'] = $channel;
        return $this;
    }

    public function getId()
    {
        return $this->user['id'];
    }

    public function getNickname()
    {
        return $this->user['nickname'];
    }

    public function getEmail()
    {
        return $this->user['email'];
    }

    public function getAvatar()
    {
        return $this->user['avatar'];
    }

    /**
     * @return AccessToken
     */
    public function getToken()
    {
        return $this->user['token'];
    }

    public function getChannel()
    {
        return $this->user['channel'];
    }

    public function getRaw($name = null)
    {
        if (is_null($name)) {
            return $this->raw;
        } else {
            return $this->raw[$name];
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->user);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->user[$offset];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {

    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {

    }
}
