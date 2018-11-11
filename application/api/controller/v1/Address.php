<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 15:39
 */

namespace app\api\controller\v1;

//收货地址
use app\api\model\UserAddress;
use app\api\validate\NewAddress;

use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\SucessException;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;
use think\Controller;


class Address extends BaseController
{

    protected $beforeActionList = [
        'checkPrimaryScope'=>['only'=>'createorupdateaddress'],

    ];


    //验证token令牌的scope权限是不是大于16大于我们就放行让他访问这个接口




    //获取用户的收货地址
    public function getUserAddress(){
        $uid = TokenService::getCurrentUid();

        $userAddress = UserAddress::where('user_id','=',$uid)->find();

        if(!$userAddress){
            throw  new UserException([
               'msg'=>'用户地址不存在',
                'errorCode'=>60001
            ]);
        }

        return $userAddress;


    }


    //写入或更新收货地址
    public function createOrUpdateAddress(){

        //验证收货地址传过来的参数
        $validate = new NewAddress();
        $validate->goCheck();


        //根据Token来获取uid
        //根据uid来查找用户数据，判断用户是不是存在，如果不存在要什么收货地址，抛异常
        //获取用户在客户端提交出来的地址信息
        //根据用户信息是不是存在来判断，是不是要添加更新用户信息。

        $uid = TokenService::getCurrentUid();



        //查询用户信息
        $user = UserModel::get($uid);



        if(!$user){
            throw new UserException();
        }

        //获取用户传过来的地址信息,我们先定义用户的关联关系
        $data = input('post.');


        $dataArray  = $validate->getDateByRule($data);






        //这句话是关联模型表中的数据user_address这个表
        $userAddress = $user->address();



        if(!$userAddress){
            //如果你收货地址表不存在，那么我们就要写入他
            $user->address()->save($dataArray);

        }else{

            //否则就是存在我们就更新收货地址
            $user->address->save($dataArray);
        }

        //写入收货地址我们要返回给客户端点成功和失败

        return new SucessException();

    }
}