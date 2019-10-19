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
        if ($group = $this->app->config->get('social.route')) {

            $this->registerRoutes(function (Route $route) use ($group) {

                $controller = $this->app->config->get('social.controller');

                $route->group($group, function () use ($route) {
                    $route->get(":channel/callback/bind", '@handleSocialCallbackForBind')
                        ->name('SOCIAL_BIND_CALLBACK');

                    $route->get(":channel/callback", '@handleSocialCallback')
                        ->name('SOCIAL_CALLBACK');

                    $route->get(":channel/bind", '@redirectToSocialForBind')
                        ->name('SOCIAL_BIND');

                    $route->get(":channel", '@redirectToSocial')
                        ->name('SOCIAL');
                })->prefix($controller);
            });
        }
    }
}
