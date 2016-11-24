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

class User implements \ArrayAccess
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
        $fields = ['id', 'nickname', 'name', 'email', 'avatar'];
        $user   = [];
        array_map(function ($field) use ($raw, $map, $user) {
            if (isset($map[$field])) {
                $field = $map[$field];
            }
            $user[$field] = isset($raw[$field]) ? $raw[$field] : null;
        }, $fields);
        return new self($user, $raw);
    }

    public function setToken($token)
    {
        $this->user['token'] = $token;
    }

    public function getId()
    {
        return $this->user['id'];
    }

    public function getNickname()
    {
        return $this->user['nickname'];
    }

    public function getName()
    {
        return $this->user['name'];
    }

    public function getEmail()
    {
        return $this->user['email'];
    }

    public function getAvatar()
    {
        return $this->user['avatar'];
    }

    public function getToken()
    {
        return $this->user['token'];
    }

    public function getRaw($name = null)
    {
        if (is_null($name)) {
            return $this->raw;
        } else {
            return $this->raw[$name];
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->user);
    }

    public function offsetGet($offset)
    {
        return $this->user[$offset];
    }

    public function offsetSet($offset, $value)
    {

    }

    public function offsetUnset($offset)
    {

    }
}