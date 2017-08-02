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
use think\Request;
use think\Session;
use think\Url;
use yunwuxin\Social;

trait SocialControllerTrait
{

    protected function setRedirectUrl(Channel $social, $channel, $bind = false)
    {
        if (method_exists($this, 'getRedirectUrl')) {
            $social->setRedirectUrl($this->getRedirectUrl($channel, $bind));
        } else {
            if ($bind) {
                $route = 'SOCIAL_BIND_CALLBACK';
            } else {
                $route = 'SOCIAL_CALLBACK';
            }
            $redirectUrl = Url::build($route, ['channel' => $channel], '', true);

            $social->setRedirectUrl($redirectUrl);
        }
    }

    public function redirectToSocial($channel, $bind = false)
    {
        $social = Social::channel($channel);

        $this->setRedirectUrl($social, $channel, $bind);

        if (property_exists($this, 'scopes')) {
            $social->scopes($this->scopes);
        }

        if (method_exists($this, 'beforeRedirect')) {
            $this->beforeRedirect($social);
        }

        return $social->redirect();
    }

    public function redirectToSocialForBind($channel)
    {
        return $this->redirectToSocial($channel, true);
    }

    public function handleSocialCallback($channel)
    {
        $social = Social::channel($channel);
        $this->setRedirectUrl($social, $channel);
        $user = $social->user(Request::instance());

        $checker = Config::get('social.user_checker');
        if ($checker && is_subclass_of($checker, UserCheckerInterface::class)) {
            if ($checker::checkSocialUser($user)) {
                return redirect(Config::get('social.redirect')['complete']);
            }
        }
        Session::flash('social_user', $user);
        return redirect(Config::get('social.redirect')['register']);
    }

    public function handleSocialCallbackForBind($channel)
    {
        $social = Social::channel($channel);
        $this->setRedirectUrl($social, $channel, true);
        $user = $social->user(Request::instance());
        Session::flash('social_user', $user);
        return redirect(Config::get('social.redirect')['bind']);
    }

}