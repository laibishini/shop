<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/30
 * Time: 15:45
 */

namespace app\api\service;

//微信回调处理类，我们集成notify类重写一个方法
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\facade\Log;
use WxPay\WxPayApi;
use WxPay\WxPayNotify;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderSerivice;

class WxNotify extends WxPayNotify
{








    /*
     *  * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
	 * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
	 * @param WxPayNotifyResults $objData 回调解释出的参数
	 * @param WxPayConfigInterface $config
	 * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调*/

    //总结一句话，用这个父类函数他可以直接返回数组处理微信返回的结果
    public function NotifyProcess($objData, $config, &$msg)
    {

        $objData = $objData->GetValues();
        //如果data数据中有result_coude

        if($objData['result_code'] == 'SUCCESS'){

            //我们表中的订单号，给微信后他成功后又返回给我们了
            $orderNo = $objData['out_trade_no'];
            //加上事务功能防止库存在次减少
            Db::startTrans();
            try{

                //这一步才算是支付成功
                //我们先拿到订单号然后更新支付状态
                $order = OrderModel::where('order_no','=',$orderNo)->find();

                //如果订单号中的status字段等于1说明没有被支付呢
                if($order->status == 1){
                    //然后我们要检测一下库存，看还有没有库存了
                    $service =  new OrderSerivice();
                    //库存量检测
                    $statusStock = $service->checkOrderStock($order->id);

                    if($statusStock['pass']){
                        //如果库存量正常减库存
                        //更新order表中status支付状态字段变成2已经支付

                        $this->updateOrderStatus($order->id,true);

                        //减库存
                        $this->reduceStock($statusStock);
                    }else{

                        //库存不足我们就不需要减库存了只更新支付的状态
                        //如果是$sucess是true我们 支付状态是2如果是false 支付状态是4已经支付了库存不足

                        $this->updateOrderStatus($order->id,false);
                    }




                }

                //就是必须一个查询走完，才能进行下一个查询，防止重复减库存
                Db::commit();
                return true;//支付成功

            }catch (Exception $ex){

                Log::error($ex);
                //可以记录日志为什么sql 错了

                //给微信回复，喊他不要调用了，支付成功了

                //总到这肯定是数据库出问题了
                Db::rollback();
                return false;



            }


        }else{

            //支付失败了这只是控制不需要在发送给我通知了，应该是账户问题
            return true;
        }


    }

    //减库存

    private function reduceStock($stockStatus){

        //他是一个二维数组我们要循环他
        foreach ($stockStatus['pStatusArray'] as $singlePStatus) {

            //每个订单下有好几个产品
            //减库存
            Product::where('id','=',$singlePStatus['id'])
                ->setDec('stock',$singlePStatus['count']);//setDec方法减库存
            
        }

    }




    //更新status字段的状态
    public function updateOrderStatus($orderID,$success){

        //如果是$sucess是true我们 支付状态是2如果是false 支付状态是4已经支付了库存不足

        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;

        //更新数据库
        OrderModel::where('id','=',$orderID)
            ->update(['status'=>$status]);




    }
}