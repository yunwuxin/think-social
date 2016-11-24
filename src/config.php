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
    'channels'     => [
        'qq'     => [
            'client_id'     => '',
            'client_secret' => '',
            'redirect'      => '',
            'union'         => false //是否获取unionId
        ],
        'weibo'  => [
            'client_id'     => '',
            'client_secret' => '',
            'redirect'      => ''
        ],
        'github' => [
            'client_id'     => '',
            'client_secret' => '',
            'redirect'      => ''
        ],
        'wechat' => [
            'client_id'     => '',
            'client_secret' => '',
            'redirect'      => ''
        ]
    ],
    /* 以下为高级配置，不了解的请勿更改 */
    'route'        => false,
    'controller'   => \yunwuxin\social\Controller::class,
    'user_checker' => null,
    'redirect'     => [
        'bind'     => '/',
        'register' => '/',
        'complete' => '/'
    ]
];