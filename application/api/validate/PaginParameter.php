<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/30
 * Time: 23:59
 */

namespace app\api\validate;

//分页验证参数类

class PaginParameter extends BaseValidate
{

    protected $rule = [
      'page'=>'isPositiveInteger',
        'size'=>'isPositiveInteger',
    ];

    protected $message = [
        'page.isPositiveInteger'=>'必须是整数',
        'size.isPositiveInteger'=>'必须是整数'
    ];
}