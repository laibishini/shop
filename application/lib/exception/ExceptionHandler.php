<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/22
 * Time: 9:22
 */

namespace app\lib\exception;


use think\Exception;
use think\exception\Handle;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;

class ExceptionHandler extends Handle
{

    //定义错误码
    private $code;
    private $msg;
    //自定义错误码
    private $error_code;

    //重写错误机智

    public function render(\Exception $e){


        //定义全局异常



        if($e instanceof BaseException){

            //如果是普通异常，发送普通错误信息
            $this->code = $e->code;
            $this->error_code = $e->errorCode;
            $this->msg = $e->msg;

        }else{

            if(Config::get('app_debug')){

                //这里弄了个开关还原了系统内部错误提示,如果是开启了app_debug就显示系统内部页面错误，如果没有开启就显示Json错误
                return parent::render($e);
            }else{
                //如果是服务器内部异常不发送具体错误信息，记录好日志
                $this->code = 500;

                $this->msg = '服务器内部错误，不能告诉你';

                $this->error_code = 999;

                $this->recordErrorLog($e);
            }



        }


        //发送错误信息

        $result = [
            'msg'=>$this->msg,
            'error_code'=>$this->error_code,
            'request_url'=>Request::url(),
        ];

        //发送json格式的数据

        return json($result,$this->code);
    }


    //定义日志写入的级别
    private function recordErrorLog(\Exception $e){


        //没有更改日志的目录所以没有办法写日志初始化
        //写入日志内部错误

        Log::record($e->getMessage(),'error');
    }
}