<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/23
 * Time: 21:36
 */

namespace app\api\model;


use think\facade\Config;
use think\Model;

class Image extends BaseModel
{

//不需要的数据返回
    protected $hidden = ['from','id','update_time',"delete_time"];


    //设置读取器拼接URL地址字段

    public function getUrlAttr($value,$data){


        //以后谁要在加前缀谁就调用基类模型的方法
        return $this->prefixImgUrl($value,$data);
    }

}