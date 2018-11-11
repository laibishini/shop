<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/25
 * Time: 18:17
 */

namespace app\api\validate;


class Count extends BaseValidate
{

    protected $rule = [

        'count'=>'isPositiveInteger|between:1,15'
    ];
}