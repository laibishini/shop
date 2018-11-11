<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 15:42
 */

namespace app\api\validate;


class NewAddress extends BaseValidate
{

    protected $rule = [
        'name'=>'require|isNotEmpty',

        'province'=>'require|isNotEmpty',
        'city'=>'require|isNotEmpty',
        'country'=>'require|isNotEmpty',
        'detail'=>'require|isNotEmpty',
    ];

    ///姓名手机号省份城市地区详细地址
}

//'mobile'=>'require|isMobile',