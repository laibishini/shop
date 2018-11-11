<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/28
 * Time: 14:59
 */

namespace app\api\service;


//订单模型细分
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;

class Order
{

    //订单商品列表，也是客户端传过来的products参数

    protected $oproducts;

    //真实的商品信息包括库存量
    protected $products;

    protected $uid;


    public function place($uid,$oProducts){

        //oproducts 和products做一个对比
        //products是从数据库中查询中出来的数据
        $this->oproducts = $oProducts;

        $this->products = $this->getProductsByOrder($oProducts) ;

        $this->uid = $uid;

        //订单状态检测完成
        $status = $this->getOrderStatus();

        //如果不同过说明订单商品里面肯定没有库存了
        if(!$status['pass']){

            $status['order_id'] = -1;//订单状态标记

            return $status;//返回没有库存状态信息
        }

        //这一步就是有库存可以下单了 我们要生成订单的快照
        $orderrSnap = $this->snapOrder($status);

        //创建订单写入到数据库Order数据表中
       $order =  $this->createOrder($orderrSnap);

       //订单写入成功库存正常
       $order['pass'] = true;

       return $order;





    }


    /**
     * @param $status订单状态传入组装好的数据传入
     */
    //生成订单快照方法
    private function snapOrder($status){

        //先定义下我们要写入快照的数据
        $snap = [
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatus' =>[],
            'snapAddress'=>null,
            'snapName'=>'',
            'snapImg' =>'',

        ];
        //订单总价格整个订单
        $snap['orderPrice'] = $status['orderPrice'];
        //订单产品的总数量
        $snap['totalCount'] = $status['totalCount'];

        //单个订单商品的详细信息
        $snap['pStatus'] = $status['pStatusArray'];

        //订单地址信息我们要从数据库中查询一下 我们把地址数组变成json字符串存到数据库中,未来有需要可以使用mongodb数据库存这种json地址

        $snap['snapAddress'] = json_encode($this->getUserAddress());

        //订单列表的缩略名字
        $snap['snapName'] = $this->products[0]['name'];

        //订单列表缩略图
        $snap['snapImg'] = $this->products[0]['main_img_url'];



        //如果是超过了3个产品订单加上等这个字

        if(count($this->products )> 1){
            $snap['snapName'] .= '等';
        }




        return $snap;






    }


    //创建订单写入到数据库Order数据表中
    private function createOrder($snap){

        //开启事务
        Db::startTrans();
        try{

            $orderNo = self::makeOrderNo();

            //实例化这个订单表
            $order = new OrderModel();

            //用户的ID号码
            $order->user_id = $this->uid;

            //订单编号
            $order->order_no = $orderNo;

            //总价格
            $order->total_price = $snap['orderPrice'];

            //总数量
            $order->total_count = $snap['totalCount'];
            //缩略图
            $order->snap_img = $snap['snapImg'];

            //缩略名字
            $order->snap_name = $snap['snapName'];

            //收货地址
            $order->snap_address = $snap['snapAddress'];

            //每个订单下面的产品是一个数组我们要改变一下

            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();//保存到数据库中

            //拿到插入数据订单号的主键
            $orderID = $order->id;

            $create_time = $order->create_time;

            //然后我们要写入到关联表中order_product表中这个表是以后扩展用的

            //他下订单的时候已经给我们product_id count了

            //循环他
            foreach ($this->oproducts as &$p) {
                //把每个订单下的产品加上订单ID
                $p['order_id']=$orderID;


            }

            $orderProduct = new OrderProduct();

            //加上order_id这个字段以后我们保存的是一组数据
            $orderProduct->saveAll($this->oproducts);

            //写入数据成功返回给服务端订单号，订单的ID号，创建的时间

            //提交事物
            Db::commit();
            return [
                'order_no' =>$orderNo,
                'order_id'=>$orderID,
                'create_time'=>$create_time,

            ];

        }catch (Exception $e){

            //回滚事物
            Db::rollback();
            throw $e;
        }








    }

    //生成订单号码通用方法
    public static function makeOrderNo(){

        $yCode = array('A','B','C','D','E','F','G','H','I','J');

        $orderSn = $yCode[intval(date('Y') - 2018)].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));





        return $orderSn;
    }

    //查询数据库有没有这个收货地址
    private function getUserAddress(){

        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();

        //如果不存在这个地址
        if(!$userAddress){
            throw new UserException([
                'msg'=>'用户收货地址不存在，下单失败了',
                'errorCode' =>60001,
            ]);

        }

        //如果有这个地址Ok

        return $userAddress->toArray();
    }

    //外部调用库存量检测单独的方法

    /**
     * @param $orderID传入下单以后写入订单表的order表主键ID号
     * @return array
     */
    public function checkOrderStock($orderID){
        //传入订单号的ID
        //查询出订单号的ID 查询order_product关联表
        $oproducts = OrderProduct::where('order_id','=',$orderID)->select();

        //查询出来的oProduct
        $this->oproducts = $oproducts;

        //然后我们依然在查询有没有这个商品
        $this->products = $this->getProductsByOrder($oproducts);

        //调用库存信息状态信息
        $status = $this->getOrderStatus();

        return $status;



    }

    //获得订单总的状态库存量和详细信息
    private function getOrderStatus(){

        //返回一个总的订单状态
        $status = [
            'pass'=>true,
            'orderPrice'=>0,//总价格
            'totalCount'=>0,
            'pStatusArray'=>[],//单个订单商品的详细信息
        ];

        //循环传过来的订单信息
        foreach ($this->oproducts as $oProduct){



            //循环的是每组的订单信息[product_id,count]
            $pStatus = $this->getProductStatus($oProduct['product_id'],$oProduct['count'],$this->products);



            //我们拿到了单个产品是不是有这个产品和库存量
            //
            //然后我们组装整个订单的数据
            //
            //我们先判断一下只要有一个产品的订单没有存储我们总订单设置false不能支付
            if(!$pStatus['haveStock']){
                $status['pass'] = false;

            }

            //每个1个数组数据就是一个商品每个商品的总价加起来就是总订单信息
            $status['orderPrice'] += $pStatus['totalPrice'];

            //订单的总价格
            $status['totalCount'] += $pStatus['counts'];

            array_push($status['pStatusArray'],$pStatus);//生成的商品信息放到整个订单组了



        }



        return $status;

    }

    //重点就是传过去真实的查询出的产品product 和我们下订单的产品ID查一下看有没有这个产品和库存就这么简单
    //每个产品订单的状态 这个方法我们要比对有没有单个商品有没有库存了和商品是存在的我们在能生成订单
    private function getProductStatus($oPID,$ocount,$products){

        $pIndex = -1;

        $pStatus = [
            'id'=>null,
            'haveStock'=>false,
            'counts'=>0,
            'price'=>0,
            'totalPrice'=>0,//单个产品的总价
            'main_img_url'=>null,
        ];//每个产品的订单信息

        //循环订单从数据库中查询出来的订单
        for($i=0;$i<count($products);$i++){
            //如果下订单的opid在我们数据库订单数组中不存在
            //如果你传过来的产品ID和我们的查询ID是有的
            if($oPID == $products[$i]['id']){
                $pIndex = $i;
                //记录一下说明表里有这个产品
            }
        }


        if($pIndex == -1){
            throw  new OrderException([
                'msg'=>'商品不存在，创建订单失败',
            ]);
        }else{

            //否则就是走到这里是有产品的

            //生成单个产品的信息
            $product = $products[$pIndex];


            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['counts'] = $ocount;
            $pStatus['price']=$product['price'];

            //单个产品的总价
            $pStatus['totalPrice'] = $product['price'] * $ocount;
            $pStatus['main_img_url'] = $product['main_img_url'] ;

            //如果每个商品总库存量 减去你下的订单量 大于0说明有库存
            if($product['stock'] - $ocount >=0){
                //单个订单有库存可以下订单
                $pStatus['haveStock'] =true;
            }



        }



        return $pStatus;


    }

    //根据订单的信息查询真实的商品信息

    /**
     * @param $oproducts
     * @return mixed
     */
    private function getProductsByOrder($oproducts){

        //这个订单发过来是一个数组我们先把product_id值放到一起然后在查询数组
        $oPIDs = [];
        foreach ($oproducts as $item){
            array_push($oPIDs,$item['product_id']);
        }

        //查询数据库

        $products = Product::all($oPIDs)
        ->visible(['id','price','stock','name','main_img_url'])
        ->toArray();



        return $products;


    }


    //发送订单消息

    /**
     * @param $orderID订单号
     * @param string $jumpPage跳转页面
     */
    public function delivery($orderID,$jumpPage=''){
        //查询数据库有没有这个订单
        $order = OrderModel::where('id','=',$orderID)
            ->find();
        //如果不存在说明订单没有
        if(!$order){
            throw new OrderException();
        }

        if($order->status != OrderStatusEnum::PAID){

            throw new OrderException([
               'msg'=>'还没有付款，你想干嘛',
                'errorCode'=>80002,
                'code'=>403
            ]);
        }

        //走到这说明付款了要发货了
        $order->status = OrderStatusEnum::DELIVERED;

        //把他变成3已经发货了在数据库中

        $order->save();//保存发货的状态


        //发送发货的信息
        $message = new DeliveryMessage();

        return $message->sendDeliveryMessage($order,$jumpPage);




    }

}