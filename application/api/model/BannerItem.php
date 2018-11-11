<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/23
 * Time: 20:27
 */

namespace app\api\model;


use think\Model;

class BannerItem extends BaseModel
{

    //不需要的数据返回
    protected $hidden = ['update_time',"delete_time",'banner_id','id','img_id'];

    //定义Image关联关系

    public function img(){

        return $this->belongsTo('Image','img_id','id');
    }
}