<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/26
 * Time: 19:48
 */

namespace app\lib\exception;

//微信错误
class WeChatException extends BaseException
{


    public $code = 404;

    //错误信息
    public $msg = '微信内部错误';

    //自定义的错误码

    public $errorCode = 30000;
}