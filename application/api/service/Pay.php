<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/29
 * Time: 15:04
 */

namespace app\api\service;




//发起支付调用

use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use app\api\service\Order as OrderSerivce;

use app\api\model\Order as OrderModel;
use think\facade\Log;
use think\Loader;
use WxPay\WxPayApi;
use WxPay\WxPayConfig;
use WxPay\WxPayJsApiPay;
use WxPay\WxPayUnifiedOrder;

//引入微信支付类文件

class Pay
{

    //商品订单ID号
    private $orderID;

    private $orderNO;

    function __construct($orderID)
    {
        if(!$orderID){
            throw new Exception('订单号不能为空');
        }

        $this->orderID = $orderID;
    }


    //调用我们的支付接口以后，我们还要对商品的库存进行检测，因为支付不是当时进行的有可能是，未来的某个时段。


    public function pay(){

        //有几种情况我们要检测一下
        //1订单号有可能根本不存在
        //2订单号确实是存在的，但是订单号和当前的用户是不匹配的
        //3订单有可能已经被支付了
        //4所以我们要对库存量在次的进行检测

        $this->checkOrderValidate();
        $orderSerivce = new OrderSerivce();

        //返回订单库存的检测状态
        $status = $orderSerivce->checkOrderStock($this->orderID);

        if(!$status['pass']){

            //如果订单库存检测有false返回订单信息不能走下一步了
            return $status;
        }

        //订单库存正常可以支付

        return $this->makeWxPreOrder($status['orderPrice']);


    }

    //生成预付订单准备给微信支付接口发送
    private function makeWxPreOrder($totalPrice){

        //发给微信接口我们要有openid 在缓存中拿到openid

        $openid = Token::getCurrentTokenVar('openid');

        if(!$openid){
            throw new TokenException();
        }

        //微信类库文件准备好

        $wxorderData =  new WxPayUnifiedOrder();

        //数据库中的订单编号也要传过去
        $wxorderData->SetOut_trade_no($this->orderNO);

        //交易类型微信开发者，统一下单文档中有
        $wxorderData->SetTrade_type('JSAPI');

        //order下单记录表中的总价格，我们已经查询出来了 微信单价是分来计算的，我们要乘以100
        $wxorderData->SetTotal_fee($totalPrice *100);

        //小程序内容信息，随意写
        $wxorderData->SetBody('胡浩商店');

        //唯一openid号码我们在缓存中，已经查到了
        $wxorderData->SetOpenid($openid);

        //最后成功和失败的回调接收通知的地址
        $wxorderData->SetNotify_url(config('secure.pay_back_url'));

        //正式发送给服务器预订单，然后给我们返回参数

        $this->getPaySignature($wxorderData);

         return $this->getPaySignature($wxorderData);



    }


    //完成数据的组装以后我们正式的要发送，给微信服务器发送预订单信息了

    private function getPaySignature($wxorderData){

        $WxPayConfig = new WxPayConfig();
        //调用sdk内置发送http请求让微信服务器生成预订单，给我参数
        $wxOrder = WxPayApi::unifiedOrder($WxPayConfig,$wxorderData);

        if($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        ){

            //如果没有success说明支付没有成功写入日志
            Log::record($wxOrder,'error');
            Log::record('订单支付失败','error');


        }

        //走到这一步微信把生成的预订单的参数返回给我们了
        //其中就有生成的sgn签名参数和prepay_id这个是支付成功后通知客户的可以用这个发送消息



        //给客户端生成返回来的数组组装和加密sing返回给客户端，让客户端拉起微信支付

        $sing = $this->sing($wxOrder);

        //把prepay_id跟新到下单order表中，以后要用他来给客户端发送消息，支付发货了，这类的
        $this->recordPreOrder($wxOrder);

        return $sing;

    }


    //我们拿到微信支付给我我们的返回参数我们要返回给微信客户端
    private function sing($wxOrder){

        //在这里我们的微信小程序sdk给我们了一个类来帮我们组装参数返回
        $jsApiPayData = new WxPayJsApiPay();

        //客户端需要微信的app_id这个小程序开发文档支付api有说明
        $jsApiPayData->SetAppid(config('winxin.app_id'));

        //当前的时间戳，是字符串的类型的
        $jsApiPayData->SetTimeStamp((string)time());

        $rand = md5(time().mt_rand(0,1000));
        //随机的字符串
        $jsApiPayData->SetNonceStr($rand);

        //统一下单接口返回的 prepay_id 参数值这个可以用来做提示用户信息，让客户端这个是后话，现在微信服务器要这个
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);

        //类型签名，可以不填，看文档
        $jsApiPayData->SetPaySign('md5');

        //最后生成sign签名算法，这个类也提供了
        $sign = $jsApiPayData->MakeSign();

        //最后客户端要的是一个数组数据，这个SDK类也提供了
        $rawValues = $jsApiPayData->GetValues();

        //在最后我们要把生成的签名算法也加上
        $rawValues['paySign'] = $sign;

        //有个数据是不需要的就是appid这个参数你传给客户端也没有用
        unset($rawValues['appId']);

        //返回数据正式结束

        return $rawValues;


    }

    //把prepay_id和这个参数保存到order表中的prepay_id这个字段下，我们是更新表不是插入表

    private function recordPreOrder($wxOrder){

        //调用数据模型
        OrderModel::where('id','=',$this->orderID)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);

    }



    //三种订单支付情况的检测
    private function checkOrderValidate(){


        //订单表检测
        $order = OrderModel::where('id','=',$this->orderID)->find();
        //1订单号有可能根本不存在查询数据库
        if(!$order){

            throw new OrderException();
        }

        //2订单号确实是存在的，但是订单号和当前的用户是不匹配的

        //这步我们需要和缓存中的uid进行比较 我们这个方法写在token中了

        if(!Token::isValidateOperate($order->user_id)){

            throw new OrderException([
                'msg'=>'订单和用户是不匹配的',
                'errorCode'=>10003,

            ]);


        }

        //3订单有可能已经被支付了 我们写一个枚举表不等于1就是支付了

        if($order->status != OrderStatusEnum::UNPAID){
            //说明没有支付
            throw new OrderException([
                'msg'=>'订单已经支付了',
                'code'=>400,
                'errorCode'=>80003
            ]);
        }


        //把订单号赋值
        $this->orderNO = $order->order_no;


    }



}