<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/26
 * Time: 11:42
 */

namespace app\api\controller\v1;

//令牌类验证
use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameteException;
use app\api\service\Token as TokenSerivce;


class Token
{

    public function getToken($code=''){

        (new TokenGet())->goCheck();

        //调用service模型层调用微信api

        $ut = new UserToken($code);

        $token = $ut->get();


        //返回的是一个字符串我们加工一下
        return ['token'=>$token];
    }

    //验证令牌是不是有效期内的
    public function verifyToken($token=''){


        if(!$token){
            throw  new ParameteException([
               'msg'=>'token不能为空'
            ]);
        }

        //验证token是不是存在
        $valid = TokenSerivce::verifyToken($token);

        return [
            'isValid'=>$valid,
        ];
    }

    /**
     * @param string $ac 第三方获取令牌
     * @param string $se
     *
     */
    public function getAppToken($ac='',$se=''){

        (new AppTokenGet())->goCheck();

        $app = new AppToken();
        $token = $app->get($ac,$se);

        return [
            'token'=>$token
        ];



    }
}