<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/24
 * Time: 21:38
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{


    public $code = 404;

    //错误信息
    public $msg = '指定的主题,不存在请检查ID';

    //自定义的错误码

    public $errorCode = 30000;
}