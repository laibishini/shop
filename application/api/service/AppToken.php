<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/11/6
 * Time: 12:23
 */

namespace app\api\service;


use app\api\model\ThirdApp;
use app\lib\exception\TokenException;

class AppToken extends Token
{

    //定义一个业务模型方法
    public function get($ac,$se){
        //验证账号密码是不是正确
        $app = ThirdApp::check($ac,$se);

        if(!$app){
            throw  new TokenException([
               'msg'=>'授权失败',
                'errorCode'=>10004
            ]);

        }else{
            $scope = $app->scope;
            $uid = $app->id;
            $values = [
              'scope'=>$scope,
                'uid'=>$uid
            ];

            $token = $this->saveToCache($values);
            return $token;
        }
    }

    //查询到账号密码保存到缓存token中
    private function saveToCache($values){
        //拿到token
        $token = self::generateToken();
        $expire_in = config('sttings.token_expire_in');
        //设置缓存时间
        $result = cache($token,json_encode($values),$expire_in);
        if(!$result){
            throw  new TokenException([
               'msg'=>'服务器缓存异常',
                'errorCode'=>10005,
            ]);
        }

        return $result;
    }
}