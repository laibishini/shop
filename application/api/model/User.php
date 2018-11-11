<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/26
 * Time: 12:19
 */

namespace app\api\model;


//用户表
class User extends BaseModel
{

    //user_address 表是有关联关系的我们用hasOne方法来定义关联关系表，只有用户表没有定义user_address的主键所以我们用hasOne

    public function address()
    {
        return $this->hasOne('UserAddress','user_id','id');


    }

    //查询令牌

    public static function getByOpenID($openid){
        //查询数据库看有没有这个opneid;

        $result= self::where('openid','=',$openid)->find();

        return $result;

    }
}