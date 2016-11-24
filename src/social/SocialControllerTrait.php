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
use think\Url;
use yunwuxin\Social;

trait SocialControllerTrait
{

    public function redirectToSocial($channel, $bind = false)
    {
        $social = Social::channel($channel);

        if ($bind) {
            $route = 'SOCIAL_BIND_CALLBACK';
        } else {
            $route = 'SOCIAL_CALLBACK';
        }
        $redirectUrl = Url::build($route, ['channel' => $channel], '', true);

        $social->withRedirectUrl($redirectUrl);

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
        $user = Social::channel($channel)->user();

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
        $user = Social::channel($channel)->user();
        Session::flash('social_user', $user);
        return redirect(Config::get('social.redirect')['bind']);
    }

}