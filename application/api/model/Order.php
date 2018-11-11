<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/28
 * Time: 22:48
 */

namespace app\api\model;

//订单表

class Order extends BaseModel
{


    protected $autoWriteTimestamp = true;

    public static function getSummaryByUser ($uid,$page,$size){

        //订单表中查询数据库
        $paginData = self::where($uid,'=','user_id')
            ->order('create_time desc')
            ->paginate($size,true,['page'=>$page]);

        //返回
        return $paginData;
    }


    //读取器
    public function getSnapItemsAttr($value){

        if(empty($value)){
            return null;
        }

        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }

        return json_decode($value);
    }

    public static function getSummaryByPage($page=1,$size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size,true,['page'=>$page]);

        return $pagingData;
    }
}