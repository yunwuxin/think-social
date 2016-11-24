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

use think\Config;
use think\Hook;
use think\Route;

function social_url($channel, $bind = false)
{
    if ($bind) {
        \think\Url::build('SOCIAL_BIND', ['channel' => $channel]);
    } else {
        \think\Url::build('SOCIAL', ['channel' => $channel]);
    }
}

Hook::add('app_init', function () {
    //注册路由
    if ($route = Config::get('social.route')) {

        Route::get([
            "SOCIAL_BIND_CALLBACK",
            "{$route}/:channel/callback"
        ], '\\yunwuxin\\social\\Controller@handleSocialCallbackForBind');

        Route::get([
            "SOCIAL_CALLBACK",
            "{$route}/:channel/callback"
        ], '\\yunwuxin\\social\\Controller@handleSocialCallback');

        Route::get([
            "SOCIAL_BIND",
            "{$route}/:channel/bind"
        ], '\\yunwuxin\\social\\Controller@redirectToSocialForBind');

        Route::get(["SOCIAL", "{$route}/:channel"], '\\yunwuxin\\social\\Controller@redirectToSocial');

    }
});