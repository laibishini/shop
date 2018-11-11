<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/28
 * Time: 13:30
 */

namespace app\api\validate;


//订单复杂验证器

use app\lib\exception\ParameteException;
use app\lib\exception\ProductException;

class OrderPlace extends BaseValidate
{


    //定义验证器规则
    protected $rule = [
        'products' =>'checkProducts',
    ];

    protected $singRule = [
        'product_id' =>'require|isPositiveInteger',
        'count' =>'require|isPositiveInteger'
    ];

    protected function checkProducts($values){

        //如果他不是数组
        if(!is_array($values)){
            throw new ParameteException([
               'msg'=>'商品参数不正确'
            ]);
        }

        //如果他为空
        if(empty($values)){
            throw new ParameteException([
                'msg'=>'商品不能为空'
            ]);
        }

        //然后循环数组中的每个数组
        foreach ($values as $value){

            $this->checkProduct($value);
        }

        return true;

    }

    protected function checkProduct($value){

        $validate = new BaseValidate($this->singRule);
        $result = $validate->check($value);

        if(!$result){
            throw new ProductException([
                'msg'=>'商品列表参数错误Interger,'
            ]);
        }
    }





}