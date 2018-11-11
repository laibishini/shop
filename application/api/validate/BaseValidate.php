<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/21
 * Time: 15:17
 */

namespace app\api\validate;


use app\lib\exception\ParameteException;
use think\Exception;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{

    public function goCheck(){

        //获取参数传过来的
        $param = Request::param();


        $result = $this->check($param);

        //参数抛异常
        if(!$result){

           $e =  new ParameteException([
               'msg'=>$this->error,
           ]);




            throw $e;
        }else{
            return true;
        }




    }


    //判断ID是不是一个正整数

    protected function isPositiveInteger($value,$rule='',$data='',$field=''){

    if(is_numeric($value) && is_int($value + 0) && ($value + 0 )>0){
        return true;
    }else{
        return false;
    }

}


    //判断token $code是不是有值

    protected function isEmptyCode($value,$rule='',$data='',$field=''){


        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }


    //设置一个通用的收货地址类的大规模验证只要我们指定的数据
    public function getDateByRule($array){

        //判断一下参数包含有没有非法的参数
        if(array_key_exists('uid',$array) || array_key_exists('user_id',$array)){
            //如果你传过来的东西有这个些我们认为你是非法的，这些是cache里获取到的
            throw  new ParameteException([
                'msg'=>'参数中包含了非法的,uid或者user_id'
            ]);

        }

        //没有就是按照我们的规定的参数传过来的，我们就要循环他

        $newarray = [];


        //把穿过来的数据重新循环写一下
        foreach ($this->rule as $key=>$value){

            $newarray[$key] = $array[$key];
        }




        return $newarray;


    }

    //定义验证手机号
    protected function isMobile($value){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';

        $result = preg_match($rule,$value);

        if($result){
            //OK验证通过
            return true;
        }else{
            return false;
        }
    }

    //定义是不是为空
    protected function isNotEmpty($value,$rule='',$data='',$field=''){


        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }

}