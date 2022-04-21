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

use think\Request;
use yunwuxin\Social;

trait SocialControllerTrait
{
    public function redirectToSocial(Request $request, Social $social, $channel)
    {
        $channel = $social->channel($channel);
        if ($request->has('redirect_uri')) {
            $channel->setRedirectUrl($request->param('redirect_uri'));
        }
        return $channel->redirect();
    }

    public function handleSocialCallback(Request $request, $channel)
    {
        $message = json_encode([
            'source'  => 'social',
            'payload' => [
                'channel' => $channel,
                'code'    => $request->param('code'),
                'state'   => $request->param('state'),
            ],
        ]);

        return "<script>window.opener.postMessage({$message},'*');window.close();</script>";
    }

}
