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

use yunwuxin\Social;

trait SocialControllerTrait
{
    public function redirectToSocial(Social $social, $channel)
    {
        return $social->channel($channel)->redirect();
    }

    public function handleSocialCallback(Social $social, $channel)
    {
        $user    = $social->channel($channel)->user();
        $message = json_encode([
            'source' => 'social',
            'user'   => serialize($user),
        ]);

        return "<script>window.opener.postMessage({$message},'*');window.close();</script>";
    }

}
