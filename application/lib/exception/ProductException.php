<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/25
 * Time: 18:30
 */

namespace app\lib\exception;


class ProductException extends BaseException
{


    public $code = 404;

    //错误信息
    public $msg = '指定的热门主题，不存在请检查参数ID';

    //自定义的错误码

    public $errorCode = 30000;
}