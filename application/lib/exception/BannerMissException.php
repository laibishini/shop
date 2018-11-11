<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/22
 * Time: 9:18
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{

    public $code = 404;

    //错误信息
    public $msg = '请求的Banner不存在';

    //自定义的错误码

    public $errorCode = 40000;


}