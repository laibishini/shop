<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/28
 * Time: 16:31
 */

namespace app\lib\exception;

//订单错误信息
class OrderException extends BaseException
{


    public $code = 403;

    //错误信息
    public $msg = '订单不存在';

    //自定义的错误码

    public $errorCode = 80000;


}