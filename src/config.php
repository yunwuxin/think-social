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

return [
    'channels'   => [
        'qq'       => [
            'client_id'     => '',
            'client_secret' => '',
        ],
        'weibo'    => [
            'client_id'     => '',
            'client_secret' => '',
        ],
        'github'   => [
            'client_id'     => '',
            'client_secret' => '',
        ],
        'wechat'   => [
            'client_id'     => '',
            'client_secret' => '',
        ],
        'topthink' => [
            'client_id'     => '',
            'client_secret' => '',
        ],
    ],
    'route'      => false,
    'controller' => \yunwuxin\social\Controller::class,
];
