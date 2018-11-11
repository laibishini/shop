<?php

/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/21
 * Time: 21:03
 */
namespace app\api\model;

//banner数据库
use think\Db;
use think\Exception;
use think\Model;

class Banner extends BaseModel
{

    //不需要的数据返回
    protected $hidden = ['update_time',"delete_time"];

    //定义关联模型数据表函数Banner_item

    public function item(){

        return $this->hasMany('BannerItem','banner_id','id');
    }


    public static function getBannerByID($id){

        $Banner = self::with(['item','item.img'])->find($id);


       return $Banner;



    }

}