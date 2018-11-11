<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/25
 * Time: 21:32
 */

namespace app\api\model;


class Category extends BaseModel
{

    protected $hidden = [
        "delete_time",
        "description",
        "update_time",
    ];

    //设置关联模型
    public function Img(){

        return $this->belongsTo('Image','topic_img_id','id');
    }
}