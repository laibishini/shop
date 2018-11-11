<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/29
 * Time: 14:57
 */

namespace app\api\controller\v1;


//订单支付类主要
use app\api\service\WxNotify;
use app\api\validate\IDMustBePostivelnt;
use WxPay\fWxPayUnifiedOrder;
use WxPay\WxPayConfig;
use WxPay\WxPayDataBaseSignMd5;
use WxPay\WxPayNotify;
use WxPay\WxPayUnifiedOrder;
use app\api\service\Pay as PayModel;


class Pay extends BaseController
{


    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'getpreorder'],
    ];

    //小程序传过来参数，订单编号，我们进行调用微信支付
    //我们在调用支付前先要检测一下，scope这个作用域是是不是16这个普通用户
    public function getPreOrder($id=''){

        //验证他不是正整数
        (new IDMustBePostivelnt())->goCheck();

        //微信支付对象

        $pay = new PayModel($id);

        //返回给客户端需要的预订单的参数，让他拉起微信支付
         return $pay->pay();





    }


    //这个方法是支付成功以后微信给我们返回异步的通知结果

    public function receiveNotify(){

        //微信通知单位调用是秒 15/30/18值到3600秒之内你接收到通知了就不在调用这个函数了

        //完成回调以后我们要做这几件事情

        //1：检查库存的数量，他不是超卖
        //2:更新这个订单的status的状态
        //3:减库存

        //如果成功处理，我们返回微信成功处理的信息，否则，我们需要返回没有成功处理

        //返回的信息特点 post方式，xml格式，没有携带参数 sdk内置类会帮我们处理xml


        $config = new WxPayConfig();
        $WxNotify = new WxNotify();

        $WxNotify->Handle($config,false);




    }


}