<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/25
 * Time: 21:42
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{

    public $code = 404;

    //错误信息
    public $msg = '没有查询到分类信息';

    //自定义的错误码

    public $errorCode = 50000;

}