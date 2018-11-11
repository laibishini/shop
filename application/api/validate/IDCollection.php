<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/24
 * Time: 18:58
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{


    protected $rule = [

        'ids' =>'require|checkIds'
    ];

    //这里就是验证Theme?ids = 1,2,3是不正整数
    protected function checkIds($values){

        $values = explode(',',$values);

        //炸开以后然后我们判断有一下是不是为空
        if(empty($values)){
            return false;
        }

        //如果不为空我们判断是不是正整数得到数组，我们要每个数都要便利

        foreach ($values as $ids){

           if(!$this->isPositiveInteger($ids)){

               return false;
           }

           return true;

        }



    }


    protected $message = [
        'ids.require'=>'id必须要填写',
        'ids.checkIds'=>'id必须以逗号分隔的正整数',
    ];
}