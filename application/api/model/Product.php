<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/24
 * Time: 15:50
 */

namespace app\api\model;


//产品表
class Product extends BaseModel
{


    protected $hidden = [
        "delete_time",
            "category_id",
            "from",
            "create_time",
            "update_time",
            "pivot",
    ];


    //设置读取器拼接URL地址字段

    public function getMainImgUrlAttr($value,$data){


        //以后谁要在加前缀谁就调用基类模型的方法
        return $this->prefixImgUrl($value,$data);
    }

    //获取最新的产品信息查询
    public  static function getMostRecent($count){

        $products = self::limit($count)
            ->order('create_time desc')
            ->select();

        return $products;
    }


    //获取分类栏目下的商品信息

    public static function getProductByCategoryById($category_id){

        //查询栏目下的产品
        $products = self::where('category_id',$category_id)->select();


        return $products;

    }


    //商品详细信息关联着product_image表和product_propety两个表我们建立关联关系

    //商品详情图获取关联product_image表一对多的关系
    public function Imgs()
    {
        return $this->hasMany('ProductImage','product_id','id');
    }

    //详细说明表关联
    public function propertys(){

        return $this->hasMany('ProductProperty','product_id','id');

    }

    //获取商品详细ID信息
    public static function getProductDetail($id){


        //**********关联模型高级用法product_img表order排序问题
        //查询数据库$query有with这个方法
        $detais = self::with([
            'Imgs'=>function($query){
                $query->with('imgUrl')
                    ->order('order','asc');
            }
        ])
            ->with(['propertys'])
            ->find($id);

        return $detais;

    }

    //*************************************************************
}