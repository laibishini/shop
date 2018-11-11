<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/24
 * Time: 14:48
 */

namespace app\api\model;


use think\facade\Config;
use think\Model;

//基类模型
class BaseModel extends Model
{


    protected function prefixImgUrl($value,$data){


        //拼接路径http://www.huhao.io/images,然后判断他是不是from == 1来自本地图片
        $finalUrl = $value;

        //如果判断他是本地图片我们就把，配置里的前缀加上
        if($data['from'] == 1){
            $finalUrl = Config::get('settings.img_prefix').$value;
        }

        //如果没有直接返回
        return $finalUrl;


    }
}