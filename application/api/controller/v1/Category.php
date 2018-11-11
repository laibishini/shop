<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/25
 * Time: 21:31
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

//栏目页
class Category
{

    //获取所有的栏目列表
    public function getAllCategories(){

        //获取栏目的所有信息

        $categories = CategoryModel::all([],'Img');

        //判断一下他是不是为空
        if($categories->isEmpty()){

            throw new CategoryException();
        }
        return $categories;



    }
}