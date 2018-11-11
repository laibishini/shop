<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/11/6
 * Time: 12:28
 */

namespace app\api\model;


class ThirdApp extends BaseModel
{

    //从数据库中查询账号密码然后验证结果查询到了，就返回结果
    public static function check($ac,$se){
        $app = self::where('app_id','=',$ac)
            ->where('app_secret','=',$se)
            ->find();

        return $app;
    }

}