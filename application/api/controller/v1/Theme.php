<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/24
 * Time: 15:49
 */

namespace app\api\controller\v1;

//专题页面
use app\api\validate\IDCollection;
use app\api\validate\IDMustBePostivelnt;
use app\lib\exception\ThemeException;
use think\Controller;

use app\api\model\Theme as ThemeModel;

class Theme
{


    //查询一下 theme?id=1,2,3专题查询
    public function getSimpleList($ids=''){

        //验证逗号分隔的字符串是不是合法的validate验证器
        (new IDCollection())->goCheck();

        //分解得到来的数组theme?1,2,3

        $ids = explode(',',$ids);

        //查询数据库
        $result =  ThemeModel::with(['topicImg','headImg'])->select($ids);

        //如果查询不到我们就抛出异常





        if($result->isEmpty()){

            throw new ThemeException();

        }else{
            return $result;
        }





    }


    //查询专题列表数据
    public function getComplexOne($id){

        //然后定义路由因为我们定义的路由和专题路由是一样的要开启完全匹配路由

        //判断是不是正整数
        (new IDMustBePostivelnt())->goCheck();

        //然后查询专题列表的数据

       $result =  ThemeModel::getThemeWithProduct($id);
       if(!$result){
           throw  new ThemeException();
       }




        return $result;
    }
}