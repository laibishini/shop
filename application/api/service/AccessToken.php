<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/11/6
 * Time: 15:39
 */

namespace app\api\service;

//获取微信需要的token
use think\Exception;
use think\facade\Cache;

class AccessToken
{
    //我们请求一次token就可以了放到缓存中，没必要请求多次
    private $tokenUrl;
    const  TOKEN_CACHED_KEY = 'access';
    //设置缓存的时间限制

    const TOKEN_EXPIRE_IN = 7000;
    //详细看文档
    function __construct()
    {
        $url = config('wx.access_token_url');

        $url = sprintf($url,config('wx.app_id'),config('wx.app_secret'));

        $this->tokenUrl = $url;

        //把组装号的请求链接参数一起保存到tokenurl中
    }


    //请求
    public function get(){

        //从缓存中获取access_token
        $token = $this->getFromCache();

        if(!$token){
            //如果缓存没有token在从服务器获取
            return $this->getFromWxServer();
        }else{
            return $token;
        }
    }

    private function getFromCache(){
        $token = cache(self::TOKEN_CACHED_KEY);
        //如果没有值返回回去
        if(!$token){
            return null;
        }

        return $token;
    }

    //开始从微信服务器中获取 token

    private function getFromWxServer(){
        $curl = \HttpRequest::getInstance($this->tokenUrl);
        //发送请求
        $token = $curl->send();

        //序列化一下、变成数组
        $token = json_decode($token,true);

        if(!$token){
            throw  new Exception('获取AccessToken异常');
        }
        //如果errcode不为空说明有值
        if(!empty($token['errcode'])){
            throw new Exception($token['errmsg']);
        }

        //保存缓存中
        $this->savaToCache($token);

        return $token['access_token'];
    }

    private function savaToCache($token){

        //保存在缓存中过期时间
        Cache::set(self::TOKEN_CACHED_KEY,$token,self::TOKEN_EXPIRE_IN);
    }

}