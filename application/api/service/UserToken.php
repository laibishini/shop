<?php

/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/26
 * Time: 12:21
 */

namespace app\api\service;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use HttpRequest;
use think\Exception;
use app\api\model\User as UserModel;

//复杂的模型处理逻辑层 token令牌生成类:主要就是用curl发送请求的URL地址微信返回appid然后我们把appid写入到数据库，然后我们在生成一个32位的随机字符串返回给客户端完成token的生成
class UserToken extends Token
{


    //初始化一下他
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    //初始化绑定url请求数据层传过来的code码
    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('winxin.app_id');
        $this->wxAppSecret = config('winxin.app_secret');

        //绑定要获取微信参数的值在weixin配置文件中
        $this->wxLoginUrl = sprintf(config('winxin.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);


    }


    //发送请求获取appid 和session_key
    public function get(){

        //发送请求

        $curl = HttpRequest::getInstance($this->wxLoginUrl);

        $result = $curl->send();


        //返回的是Json让他变成数组true代表返回的是数组
        $wxResult = json_decode($result,true);


        //如果微信返回的是空
        if(empty($wxResult)){
            throw  new Exception('获取session_key和appID异常,微信内部错误');
        }else{


            //请求URL回来看是不是有errcode有这个错误代码也是有问题的
            $loginFail = array_key_exists('errcode',$wxResult);
            //如果存在这次调用是失败的
            if($loginFail){

                //如果有报微信内部错误,他自己返回的Json有错误信息
                $this->processLoginError($wxResult);
            }else{

                //如果没有错误我们就给他颁发令牌
                 return $this->granToken($wxResult);

            }


        }






    }


    //定义颁发令牌
    private function granToken($wxResult){

        //拿到 openid
        //数据库看一下，我们这个openid是不是存在
        //如果存在我们就不处理，如果不存在我们就新增一条数据user表 openid数据
        //生成令牌，准备缓存数据，写入缓存
        //把令牌给客户端

        //写入缓存我们要注意以下几点
        //1 他是key value形式
        //2 key:令牌
        //3 value : wxResult,uid scope他是等级用户等级标识不是所有用户都可以获取所有接口的
        $openid = $wxResult['openid'];//用户唯一标识

        //查询是不是存在的
        $user = UserModel::getByOpenID($openid);

        if($user){

            //如果这个openid是存在的拿到他的索引ID号
            $uid = $user->id;

        }else{
            //如果不存在我们就在user表中创建

            $uid = $this->newUser($openid);


        }

        //写入到缓存数据中，加快访问

        $cacheValue = $this->prepareCachedValue($wxResult,$uid);

        //调用缓存函数
        $token = $this->saveToCache($cacheValue);

        return $token;

    }

    //保存到缓存中的数据
    private function saveToCache($cacheValue){

        //保存形式key:value形式的键值对
        $key = self::generateToken();//生成Token方法我们定义一个token基础类


        //把数组变成字符串
        $value = json_encode($cacheValue);

        //设置缓存的实效时间

        $exprie = config('settings.token_expire_in');

        //用助手函数写入缓存,当然我们也可以用face门面静态类使用
        //我们也可以使用redis配置放到缓存中，他们唯一的区别就是他可以存储对象形式，
        //缓存就要把数组变成字符串来存储，不能单独存储对象的形式，稍微快一点点。
        //后期服务器大的话，我们可以更改配置cache来调用redis驱动写入。
        $request = cache($key,$value,$exprie);

        if(!$request){
            //如果缓存中不存在，我们还是要返回错误的信息

            throw new TokenException([

                'msg'=>'服务器缓存,异常',
                'errCode'=>10005
            ]);
        }

        return $key;
    }

    //组装写入缓存中的数据
    private function prepareCachedValue($wxResult,$uid){
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;

        //用户权限我们在libe目录下enum中定义了
       $cacheValue['scope']= ScopeEnum::User;//权限作用域

        return $cacheValue;
    }

    //写入User表方法
    private function newUser($openid){
        $user = UserModel::create([
            'openid'=>$openid
        ]);



        return $user->id;

    }
    //定义微信错误信息
    private function processLoginError($wxResult){


        throw new WeChatException([
            'msg'=>$wxResult['errmsg'],
            'errorCode'=>$wxResult['errcode'],
        ]);
    }
}