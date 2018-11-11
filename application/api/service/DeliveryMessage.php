<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/11/6
 * Time: 15:14
 */

namespace app\api\service;

//给客户发送收货通知消息发送模版消息信息
use app\api\model\User;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;

class DeliveryMessage extends WxMessage
{

    const DELIVERY_MSG_ID = '我申请模版的的ID号';

    public function sendDeliveryMessage($order,$tplJumpPage=''){
        if(!$order){
            //如果没有这个模型对象
            throw new OrderException();
        }
        //模版ID号码
        $this->tplID = self::DELIVERY_MSG_ID;
        //什么样的表单提交场景下，为 submit 事件带上的 formId；支付场景下，为本次支付的 prepay_id

        $this->fromID = $order->prepay_id;
        //跳转地址
        $this->page = $tplJumpPage;
        //获取模版消息
        $this->prepareMessageData($order);

        $this->emphasisKeyWord = 'keyword2.DATA';

        //调用父类的方法拿到appenid
        return parent::sendMessage($this->getUserOpenID($order->urser_id));


    }


    //模版内容
    private function prepareMessageData($order){
        $dt = new \DataTime();

       $data = ["keyword1"=> [ "value"=> "顺风速运"], "keyword2"=> ["value"=>$order->snap_name], "keyword3"=> ["value"=> $order->snap_no] , "keyword4"=> ["value"=> time()]];

       $this->data = $data;
    }

    //获取openid
    private function getUserOpenID($uid){

        $user = User::get($uid);

        if(!$user){
            throw new UserException();
        }

        return $user->openid;

    }


}