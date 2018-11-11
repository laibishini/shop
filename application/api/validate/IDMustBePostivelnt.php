<?php

/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/21
 * Time: 14:03
 */
namespace app\api\validate;
/*验证器*/
use think\Validate;

class IDMustBePostivelnt extends BaseValidate
{

    protected $rule= [
        'id' =>'require|isPositiveInteger',

    ];



    //定义验证器的错误信息
    protected $message = [
        'id.require' => 'ID必须',
        'id.isPositiveInteger' => 'ID必须是整数',

    ];

}