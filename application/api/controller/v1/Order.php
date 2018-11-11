<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/28
 * Time: 11:16
 */

namespace app\api\controller\v1;

//订单支付类
use app\api\validate\IDMustBePostivelnt;
use app\api\validate\OrderPlace;
use app\api\validate\PaginParameter;
use app\lib\exception\SucessException;
use think\Controller;
use app\api\model\Order as OrderModel;
use app\api\service\Token as TokenSerivce;
use app\api\service\Order as OrderSerivce;

class Order extends BaseController
{

    //用户在选择商品后，向api提交他所选择的商品相关的信息
    //api 在接收到信息后，需要检查订单相关商品的库存量
    //有库存，在把订单数据存入数据库中。下单成功了，返回给客户端消息，告诉客户端可以支付了。
    //调用我们的支付接口进行支付。
    //还要对我们的库存在次检测。
    //服务器这边就可以调用微信的支付接口进行支付
    //小程序根据微信返回的结果拉起微信支付
    //微信会给我们返回一个支付结果（异步的）
    //成功也需要对库存量的检查(可能超卖有库存才能减库存)
    //成功后，我们进行库存量的扣除，失败返回一个支付失败的结果

    //前置方法，我们要先检测scope不是大于16是等于16按道理管理员是不能访问支付接口的只能用户访问

    protected $beforeActionList = [

        //前置方法检测用户是不是16权限用户下单是不能管理员看的
        'checkExclusiveScope'=>['only'=>'placeorder'],
        'checkPrimaryScope'=>['only'=>'getsummarybyuser','getdetail']
    ];
    //历史订单和详细订单信息管理员是可以看的
    //支付方法
    public function placeOrder(){


        //使用验证
        (new OrderPlace())->goCheck();


        //获得订单数组
        $products = input('post.products/a');











        //从缓存中拿到uid
        $uid = TokenSerivce::getCurrentUid();

        //调用订单接口
        $OrderModel = new OrderSerivce();

        //返回结果给客户端

        //结果只有两个，第一订单写入成功返回生成的订单号，第二库存失败返回错误
        $status = $OrderModel->place($uid,$products);

        return $status;




    }

    //历史订单查询记录

    public function getSummaryByUser($page=1,$size=15){


        //验证器验证一下必须是整数
        (new PaginParameter())->goCheck();

        //获取UId 在缓存中有我们在缓存中找到
        $uid = TokenSerivce::getCurrentUid();

        //在模型中查询数据
        $paginData = OrderModel::getSummaryByUser($uid,$page,$size);

        //判断一下是不是空
        if($paginData->isEmpty()){

            return [
                'data'=>[],
                'curren_page'=>$paginData->currentPage(),
            ];
        }

        $data = $paginData->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return [
            'data'=>$data,
            'curren_page'=>$paginData->currentPage(),
        ];







    }


    //历史订单详情

    public function getDetail($id){


        //判断一下必须是整数
        (new IDMustBePostivelnt())->goCheck();

        //查询数据库订单
       $detail =  OrderModel::get($id);

       if(!$detail){
           return [];
       }

       return $detail->hidden(['prepay_id']);
    }


    //获取所有的订单
    public function getSummary($page=1,$size=20){

        (new PaginParameter())->goCheck();

        $pagingOrders = OrderModel::getSummaryByPage($page,$size);

        //如果没有返回空
        if($pagingOrders->isEmpty()){
            return [
              'current_page'=>$pagingOrders->currentPage(),
                'data'=>[]
            ];
        }

        $data = $pagingOrders->hidden(['snap_items','snap_address'])
        ->toArray();

        return [
            'current_page'=>$pagingOrders->currentPage(),
            'data'=>$data
        ];



    }


    //发送模版消息这个发送消息只能市线上付款以后给我prepay_id只能是线上时候给你返回的prepay_id才有效才能发送，其他时候不能发送

    /**
     * @param $id订单号
     * @return SucessException
     */
    public function delivery($id){
        (new IDMustBePostivelnt())->goCheck();
        $order = new OrderSerivce();
        $success = $order->delivery($id);

        if($success){
            return new SucessException();
        }

    }

}
