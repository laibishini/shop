<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 17:31
 */

namespace app\lib\exception;


class SucessException
{


    public $code = 201;

    //错误信息
    public $msg = 'ok更新成功';

    //自定义的错误码

    public $errorCode = 0;
}