<?php
namespace app\api\controller\v1;


use app\api\validate\IDMustBePostivelnt;
use app\lib\exception\BannerMissException;

use think\Controller;
use app\api\model\Banner as BannerModel;
use think\Exception;
use think\facade\Config;
use think\facade\Request;

/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/21
 * Time: 11:24
 */

/*轮播图*/
class Banner extends Controller
{

    /**
     * id 轮播图的ID号码
     */
    public function getBanner($id){



        //验证器

        (new IDMustBePostivelnt())->goCheck();

            $Banner = BannerModel::getBannerByID($id);
            //抛出异常
        if(!$Banner){
            throw new BannerMissException();
        }

            return $Banner;

    }
}