<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 17:17
 */

namespace app\lib\exception;


class UserException extends BaseException
{


    public $code = 404;

    //错误信息
    public $msg = '用户不存在';

    //自定义的错误码

    public $errorCode = 60000;

}