<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/29
 * Time: 16:58
 */

namespace app\lib\enum;


//支付状态定位
class OrderStatusEnum
{

    //待支付
    const  UNPAID = 1;

    //已经支付
    const PAID = 2;

    //已发货
    const  DELIVERED = 3;

    //已经支付，但是库存不足
    const PAID_BUT_OUT_OF = 4;



}