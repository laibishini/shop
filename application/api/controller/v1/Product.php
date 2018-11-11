<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/25
 * Time: 17:56
 */

namespace app\api\controller\v1;


//最新产品推荐
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePostivelnt;
use app\lib\exception\ProductException;

//最近新品
class Product
{

    //商品展示最多15条展示产品
    public function getRecent($count = 15){

        //需要验证
        (new Count())->goCheck();

        //下面就要获取模型里面的数据了

        $Recent = ProductModel::getMostRecent($count);

        if($Recent->isEmpty()){

            throw new ProductException();

        }



        // // 数据集返回类型 database.php配置文件中开启
        //'resultset_type'  => 'collection',

        //开启返回数据集对象形式来隐藏数据
        $Recent = $Recent->hidden(["summary"]);


        return $Recent;


    }


    //查询栏目下的商品

    public function getAllCategory($id){

        //获取栏目下面的商品信息
        $products = ProductModel::getProductByCategoryById($id);

        if($products->isEmpty()){
            throw new ProductException();
        }

        $products = $products->hidden(['summary']);

        return $products;

    }

    //商品详情
    public function getOne($id){

        //判断一下他是不是正整数
        (new IDMustBePostivelnt())->goCheck();

        //然后查询数据库

        $product = ProductModel::getProductDetail($id);

        return $product;

    }

}