<?php

namespace yunwuxin\social;

use think\Route;

class Service extends \think\Service
{
    public function boot()
    {
        //注册路由
        if ($group = $this->app->config->get('social.route')) {

            $this->registerRoutes(function (Route $route) use ($group) {
                $controller = $this->app->config->get('social.controller');

                $route->group($group, function () use ($route) {
                    $route->get(":channel/callback", '@handleSocialCallback')->name('SOCIAL_CALLBACK');
                    $route->get(":channel", '@redirectToSocial')->name('SOCIAL');
                })->prefix($controller);
            });
        }
    }
}
