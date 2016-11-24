# ThinkPHP5 社会化登录组件

## 安装

~~~
composer require yunwuxin/think-social
~~~

## 配置

配置文件位于`application/extra/social`  
目前支持4个平台的：`qq`,`weibo`,`github`,`wechat`

### 配置示例
~~~
...
  'weibo' => [
    'client_id'     => 'your-app-id',
    'client_secret' => 'your-app-secret',
    'redirect'      => 'http://localhost/auth/weibo/callback',
  ],
...
~~~


## 使用

### 路由
~~~
Route::get('auth/:channel/callback', 'Auth/handleSocialCallback');
Route::get('auth/:channel', 'Auth/redirectToSocial');
~~~

### 控制器
~~~

<?php

namespace app\controller;

use yunwuxin\Social;

class AuthController extends Controller
{

    public function redirectToSocial($channel)
    {
        return Social::channel($channel)->redirect();
        // return Social::channel($channel)->scopes(['scope1','scope2'])->redirect();
    }

    public function handleSocialCallback($channel)
    {
        $user = Social::channel($channel)->user();

        // $user->getToken();
        // $user->getId();
        // $user->getName();
        // $user->getNickname();
        // $user->getAvatar();
        // $user->getEmail();
    }
}

~~~
