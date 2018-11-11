<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/22
 * Time: 14:12
 */

namespace app\lib\exception;


use app\lib\exception\BaseException;

class ParameteException extends BaseException
{

    public $code = 400;

    //错误信息
    public $msg = '参数错误';

    //自定义的错误码

    public $errorCode = 10000;
}