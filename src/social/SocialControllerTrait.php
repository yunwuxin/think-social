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

use think\App;
use think\Config;
use think\helper\Str;
use yunwuxin\Social;
use yunwuxin\social\exception\InvalidStateException;

trait SocialControllerTrait
{
    protected function setRedirectUrl(Channel $social, $channel, $bind = false)
    {
        if (method_exists($this, 'getRedirectUrl')) {
            $redirectUrl = $this->getRedirectUrl($channel, $bind);
        } else {
            if ($bind) {
                $route = 'SOCIAL_BIND_CALLBACK';
            } else {
                $route = 'SOCIAL_CALLBACK';
            }
            $redirectUrl = url($route, ['channel' => $channel])->domain(true);
        }
        $social->setRedirectUrl($redirectUrl);
    }

    protected function getState()
    {
        return Str::random(40);
    }

    protected function isStateless(App $app, Channel $channel)
    {
        return $channel->isStateless() || !$app->exists('session');
    }

    protected function hasInvalidState(App $app, Channel $channel)
    {
        if ($this->isStateless($app, $channel)) {
            return false;
        }
        $state = $app->session->pull('state');
        return !(strlen($state) > 0 && $app->request->param('state') === $state);
    }

    public function redirectToSocial(Social $social, App $app, $channel, $bind = false)
    {
        $social = $social->channel($channel);

        $this->setRedirectUrl($social, $channel, $bind);

        if (property_exists($this, 'scopes')) {
            $social->scopes($this->scopes);
        }

        if (method_exists($this, 'beforeRedirect')) {
            $this->beforeRedirect($social);
        }

        if ($this->isStateless($app, $social)) {
            $app->session->set('state', $state = $this->getState());
            $social->with([
                'state' => $state,
            ]);
        }

        return $social->redirect();
    }

    public function redirectToSocialForBind(Social $social, $channel)
    {
        return $this->redirectToSocial($social, $channel, true);
    }

    protected function getUser(App $app, Social $social, $channel)
    {
        $social = $social->channel($channel);
        $this->setRedirectUrl($social, $channel);

        if ($this->hasInvalidState($app, $social)) {
            throw new InvalidStateException;
        }

        return $social->user();
    }

    public function handleSocialCallback(Social $social, App $app, Config $config, $channel)
    {
        $user = $this->getUser($app, $social, $channel);

        if ($social->checkUser($user)) {
            return redirect($config->get('social.redirect.complete'))->restore();
        }

        $social->setFlashUser($user);

        return redirect($config->get('social.redirect.register'));
    }

    public function handleSocialCallbackForBind(Social $social, App $app, Config $config, $channel)
    {
        $user = $this->getUser($app, $social, $channel);

        $social->setFlashUser($user);

        return redirect($config->get('social.redirect.bind'));
    }

}
