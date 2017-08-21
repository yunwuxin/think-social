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
        $route = 'SOCIAL_BIND';
    } else {
        $route = 'SOCIAL';
    }
    return \think\Url::build($route, ['channel' => $channel]);
}

Hook::add('app_init', function () {
    //注册路由
    if ($route = Config::get('social.route')) {

        $controller = Config::get('social.controller');

        Route::get([
            "SOCIAL_BIND_CALLBACK",
            "{$route}/:channel/callback/bind"
        ], $controller . '@handleSocialCallbackForBind');

        Route::get([
            "SOCIAL_CALLBACK",
            "{$route}/:channel/callback"
        ], $controller . '@handleSocialCallback');

        Route::get([
            "SOCIAL_BIND",
            "{$route}/:channel/bind"
        ], $controller . '@redirectToSocialForBind');

        Route::get([
            "SOCIAL",
            "{$route}/:channel"
        ], $controller . '@redirectToSocial');

    }
});