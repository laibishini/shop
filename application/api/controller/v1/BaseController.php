<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/10/28
 * Time: 12:30
 */

namespace app\api\controller\v1;


//基础类
use think\Controller;
use app\api\service\Token as TokenSerivce;

class BaseController extends Controller
{


    //检测用户和管理员接口访问限制
    protected function checkPrimaryScope(){

        TokenSerivce::needPrimaryScope();

    }


    //检测用户不能检测管理员
    protected function checkExclusiveScope(){

        TokenSerivce::needExclusiveScope();
    }



}