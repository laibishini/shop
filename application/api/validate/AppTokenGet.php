<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/11/6
 * Time: 12:19
 */

namespace app\api\validate;

//第三方获取账号密码验证
class AppTokenGet extends BaseValidate
{
    protected $rule = [
      'ac'=>'require|isNotEmpty',
      'se'=>'require|isNotEmpty',
    ];

}