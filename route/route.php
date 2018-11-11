<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//Route::get('think', function () {
//    return 'hello,ThinkPHP5!';
//});


use think\facade\Route;

Route::rule('hello/:name', 'user/index','GET');

//Banner路由调用方式 http://www.huhao.io/api/v1/banner/1
Route::rule('api/:version/banner/:id', 'api/:version.Banner/getBanner','GET');

//专题路由 调用方式http://www.huhao.io/api/v1/theme?ids=1,2,3


Route::rule('api/:version/theme', 'api/:version.Theme/getSimpleList','GET');

//专题列表类路由 调用方式http://www.huhao.io/api/v1/theme/1

Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');

//最新产品调用方式http://www.huhao.io/api/v1/product/recent?count=10 最多15个产品
Route::get('api/:version/product/recent','api/:version.Product/getrecent');

//栏目列表路由 调用方法 http://www.huhao.io/api/v1/category/all
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');

//按照栏目ID查询下面的商品信息调用信息http://www.huhao.io/api/v1/product/by_category?id=3

Route::get('api/:version/product/by_category','api/:version.Product/getAllCategory');


//验证令牌Token类调用http://www.huhao.io/api/v1/token/user {code:code}

Route::post('api/:version/token/user','api/:version.Token/getToken');

//验证令牌是不是有效期内的

Route::post('api/:version/token/verify','api/:version.Token/verifyToken');

//cms调用地址登陆
Route::post('api/:version/token/app','api/:version.Token/getAppToken');


//商品详情调用方式http://www.huhao.io/api/v1/product/11   这个路由最后的参数是id=>/d+正则匹配:id变量限定死
Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);



//收货地址控制器

//token 6f06a22137b5772a1b411cf5f3a6b9b5

Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');

Route::get('api/:version/address','api/:version.Address/getUserAddress');


///收货地址{"name":"胡浩","mobile":"18634084350","province":"河北省","city":"保定市","country":"安国市","detail":"大五女镇"}


//订单支付order接口调用方式http://www.huhao.io/api/v1/order header头携带令牌
//{"products":{"product_id":1,"count":2}}发送订单信息
Route::post('api/:version/order','api/:version.Order/placeOrder');

//获取订单信息
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);

//获取所有订单cms调用
Route::get('api/:version/order/paginate','api/:version.Order/getSummary');

//支付成功点击发送发送模版消息，注意只能是线上支付才能发货人家才能收到消息prepay_id只能在线上有效
Route::put('api/:version/order/delivery','api/:version.Order/delivery');



//进行支付的接口调用 http://www.huhao.io/api/v1/pay/pre_order  商品的ID号码传入  返回小程序需要的参数，小程序在拉起支付


Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');





//成功后的小程序通知服务器支付成功和失败
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotify');




//历史订单记录调用方式http://www.huhao.io/api/v1/order/by_user?page=1&size=3
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');


Route::get('api/:version/order/detail/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);





















