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

use think\Config;
use think\Session;
use yunwuxin\Social;

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

    public function redirectToSocial(Social $social, $channel, $bind = false)
    {
        $social = $social->channel($channel);

        $this->setRedirectUrl($social, $channel, $bind);

        if (property_exists($this, 'scopes')) {
            $social->scopes($this->scopes);
        }

        if (method_exists($this, 'beforeRedirect')) {
            $this->beforeRedirect($social);
        }

        return $social->redirect();
    }

    public function redirectToSocialForBind(Social $social, $channel)
    {
        return $this->redirectToSocial($social, $channel, true);
    }

    protected function getUser(Social $social, $channel)
    {
        $social = $social->channel($channel);
        $this->setRedirectUrl($social, $channel);
        return $social->user();
    }

    public function handleSocialCallback(Social $social, Config $config, $channel)
    {
        $user = $this->getUser($social, $channel);

        if ($social->checkUser($user)) {
            return redirect($config->get('social.redirect.complete'))->restore();
        }

        $social->setFlashUser($user);

        return redirect($config->get('social.redirect.register'));
    }

    public function handleSocialCallbackForBind(Social $social, Config $config, $channel)
    {
        $user = $this->getUser($social, $channel);

        $social->setFlashUser($user);

        return redirect($config->get('social.redirect.bind'));
    }

}
