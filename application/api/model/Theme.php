<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/24
 * Time: 15:50
 */

namespace app\api\model;


//专题表模型
class Theme extends BaseModel
{

    protected $hidden = ["topic_img_id","delete_time","head_img_id","update_time"];



    //定义关联关系表

    public function topicImg(){

        //定义images关联关系表 hasOne是另一张没有关联关系的表可以使用，Theme表定义了关联关系比如表中增加了topic_img_id这个表就是主表

        return $this->belongsTo('Image','topic_img_id','id');
    }

    public function headImg(){

        //定义关联关系
        return $this->belongsTo('Image','head_img_id','id');
    }

    //专题表和产品表是一多对多的关系，所以我们要用到第三章表

    public function products(){

        /*belongsToMany('关联模型','中间表','外键','关联键');
关联模型（必须）：模型名或者模型类名
中间表：默认规则是当前模型名+_+关联模型名 （可以指定模型名）
外键：中间表的当前模型外键，默认的外键名规则是关联模型名+_id
关联键：中间表的当前模型关联键名，默认规则是当前模型名+_id*/
        //这个函数就是调用多对多关系的函数

        return $this->belongsToMany('Product','theme_product',
            'product_id','theme_id');
    }


    //定义查询专题列表的数据
    public  static function getThemeWithProduct($id){

        //专题列表的关联数据有 products 产品表和关联theme_product 表 Image表
        $result = self::with(['products','topicImg','headImg'])->find($id);

        return $result;
    }
}