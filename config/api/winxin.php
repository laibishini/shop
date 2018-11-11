<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/26
 * Time: 17:46
 */

//微信配置文件

return [

    'app_id'=>'wxd513740fa6cc0148',
    'app_secret'=>'de5ca2604bc084fb737d1786fc489e3e',
    'login_url'=>'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
    'access_token_url'=>'https://api.weixin.qq.com/cgi-bin/token?grant_type=%s&appid=%s&secret=%s'
];