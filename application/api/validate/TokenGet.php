<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/26
 * Time: 11:43
 */

namespace app\api\validate;


//验证令牌validate
class TokenGet extends BaseValidate
{

    protected $rule = [
        'code'=>'require|isEmptyCode',
    ];


    protected $message = [
        'code.isEmptyCode'=>'不能传空值过来，验证不通过！'
    ];
}