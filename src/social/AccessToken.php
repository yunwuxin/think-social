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

class AccessToken
{
    protected $raw;
    protected $token;

    protected function __construct($raw, $token)
    {
        $this->raw   = $raw;
        $this->token = $token;
    }

    public function getValue()
    {
        return $this->token;
    }

    public function getRaw($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->raw;
        } else {
            return isset($this->raw[$name]) ? $this->raw[$name] : $default;
        }
    }

    public function __toString()
    {
        return $this->token;
    }

    public static function make($raw, $tokenName = 'access_token')
    {
        return new self($raw, $raw[$tokenName]);
    }
}
