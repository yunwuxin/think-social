<?php

namespace yunwuxin\social;

use think\Route;

class Service extends \think\Service
{
    public function boot()
    {
        Channel::codeResolver(function () {
            if ($this->app->request->has('code')) {
                return $this->app->request->param('code');
            }
        });

        Channel::stateResolver(function () {
            return $this->app->request->param('state');
        });

        //注册路由
        if ($route = $this->app->config->get('social.route')) {

            $this->registerRoutes(function (Route $route) {

                $controller = $this->app->config->get('social.controller');

                $route->get("{$route}/:channel/callback/bind", $controller . '@handleSocialCallbackForBind')
                    ->name('SOCIAL_BIND_CALLBACK');

                $route->get("{$route}/:channel/callback", $controller . '@handleSocialCallback')
                    ->name('SOCIAL_CALLBACK');

                $route->get("{$route}/:channel/bind", $controller . '@redirectToSocialForBind')
                    ->name('SOCIAL_BIND');

                $route->get("{$route}/:channel", $controller . '@redirectToSocial')
                    ->name('SOCIAL');
            });
        }
    }
}
