# ThinkPHP6 社会化登录组件

## 安装

~~~
composer require yunwuxin/think-social
~~~

## 配置

目前支持4个平台的：`qq`,`weibo`,`github`,`wechat`

### 配置示例

~~~
...
  'weibo' => [
    'client_id'     => 'your-app-id',
    'client_secret' => 'your-app-secret',
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

~~~php

<?php

namespace app\controller;

use yunwuxin\Social;

class AuthController extends Controller
{

    public function redirectToSocial(Social $social, $channel)
    {
        return $social->channel($channel)->redirect();
        // return $social->channel($channel)->scopes(['scope1','scope2'])->redirect();
    }

    public function handleSocialCallback(Social $social,$channel)
    {
        $user = $social->channel($channel)->user();

        // $user->getToken();
        // $user->getId();
        // $user->getName();
        // $user->getNickname();
        // $user->getAvatar();
        // $user->getEmail();
    }
}

~~~
