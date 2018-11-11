<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 10:17
 */

namespace app\lib\exception;


class TokenException extends BaseException
{

    public $code = 401;

    //错误信息
    public $msg = '没有缓存token不存在或者，已经过期，有问题';

    //自定义的错误码

    public $errorCode = 10001;

}