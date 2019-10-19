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

function social_url($channel, $bind = false)
{
    if ($bind) {
        $route = 'SOCIAL_BIND';
    } else {
        $route = 'SOCIAL';
    }
    return url($route, ['channel' => $channel]);
}
