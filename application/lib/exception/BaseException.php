<?php

/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/22
 * Time: 9:14
 */
namespace app\lib\exception;



use think\Exception;
use Throwable;

class BaseException extends Exception
{


    //HTTP 状态码 404 200

    public $code = 400;

    //错误信息
    public $msg = '参数错误';

    //自定义的错误码

    public $errorCode = 10000;


    //我们要初始化他

    public function __construct($params = [])
    {
       if(!is_array($params)){

           //如果你初始化不是数组的话我们抛一个错误
           throw Exception('参数必须是数组');
       }

       //拿到参数来写入初始化变量
        if(array_key_exists('code',$params)){

           $this->code = $params['code'];

        }
        if(array_key_exists('msg',$params)){

            $this->msg = $params['msg'];

        }

        if(array_key_exists('errorCode',$params)){

            $this->errorCode = $params['errorCode'];

        }

    }


}