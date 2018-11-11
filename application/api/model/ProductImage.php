<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/27
 * Time: 12:09
 */

namespace app\api\model;

//产品详情图表
class ProductImage extends BaseModel
{

    protected $hidden = [
        "img_id",
            "delete_time",
            "product_id",
    ];
    //产品详情图表在image表中我们要定义关联关系 他是一对一的关系
    public function imgUrl(){

        return $this->belongsTo('Image','img_id','id');
    }
}