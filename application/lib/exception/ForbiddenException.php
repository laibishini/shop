<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 23:12
 */

namespace app\lib\exception;


//权限异常
class ForbiddenException extends BaseException
{

    public $code = 403;

    //错误信息
    public $msg = '权限等级太低，不能访问';

    //自定义的错误码

    public $errorCode = 10001;
}