<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 9:35
 */

namespace app\api\service;

//这个是生成token基础类，有可能其他的类也需要生成token
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\ThemeException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;

class Token
{

    //加密返回token令牌
    public static function generateToken(){

        //使用公共函数方法common.php方法类中我们写的一个定义随机32字符串的方法

        $randChars = getRandChars(32);


        //我们这样直接返回字符串是不安全的我们用三组字符串来进行随机的加密。

        //1第二组加密 得到请求脚本服务器的时间戳
        $timeStamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //2第三组salt盐加密 在secure在敏感配置项目里面

        $salt = config('secure.token_salt');

        //用md5组合加密 三组字符串加密返回

        return md5($randChars.$timeStamp.$salt);
    }





    //用户敏感信息调用必须要token经过这里
    //首先我们要灵活一下来判断用户想要那个value中的值，我们在缓存中存了很多的值，有uid 和appid

    public static function getCurrentTokenVar($key){

        //我们先规定一下用户传过来的令牌，必须是header里面的token
        //拿到我们token在header中
        $token = Request::header('token');

         if(!$token){
             throw new ThemeException([
                 'msg'=>'请传入token'
             ]);
         }



        //获取缓存中的value值
        $vars = Cache::get($token);



        //有可能不存在
        if(!$vars){
            //抛异常
            throw new TokenException();
        }else{
            //拿到value我们必须要让他变成数组的形式
            if(!is_array($vars)){
                //必须要判断一下他是不是数组不是数组我们才能转换，是数组不用转换了。
                $vars = json_decode($vars,true);
            }


            //判断一下有没有uid在数组中有没有
            if(array_key_exists($key,$vars)){


                return $vars[$key];
            }

        }





    }

    //拿到token来获取缓存中的uid是不是存在

    public static function getCurrentUid(){

        //传递uid然后拿到用户的ID
        $uid = self::getCurrentTokenVar('uid');


        return $uid;

    }



    //用户和管理员检测接口，只能检测管理员和用户
    public static  function needPrimaryScope(){


        //拿到token令牌
        $scope = self::getCurrentTokenVar('scope');



        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            }else{

                //权限不够抛异常
                throw new ForbiddenException();
            }
        }else{
            //没有scope肯定是有问题
            throw new TokenException();
        }
    }


    //只能检测用户接口，不能检测管理员

    public static function needExclusiveScope(){


        //拿到token令牌
        $scope = self::getCurrentTokenVar('scope');



        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{

                //权限不够抛异常
                throw new ForbiddenException();
            }
        }else{
            //没有scope肯定是有问题
            throw new TokenException();
        }
    }


    //订单号和当前的用户进行比对

    public static function isValidateOperate($checkedUID){

        if(!$checkedUID){
            throw new Exception('检测必须UID传入要检查的值');
        }

        $currentOperateUID = self::getCurrentUid();

        //订单号的user_id和我们传入的进行比对
        if($currentOperateUID == $checkedUID){
            return true;
        }

        return false;


    }


    //验证token是不是存在
    public static function verifyToken($token){


        $exist = Cache::get($token);

        if($exist){
            return true;
        }else{
            return false;
        }
    }

}