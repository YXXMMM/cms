<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/test','User\UserController@test');
//Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>40300]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');


//Route::match(['get','post'],'/test/abc','Test\TestController@abc');
Route::any('/test/abc','Test\TestController@abc');


Route::get('/view/test1','Test\TestController@viewTest1');
Route::get('/view/test2','Test\TestController@viewTest2');




//用户注册
Route::get('/user/reg','User\UserController@reg');
Route::post('/user/reg','User\UserController@doReg');

Route::get('/weixin/login','Weixin\WeixinController@login');            //微信登录
Route::get('/weixin/getcode','Weixin\WeixinController@getCode');        //接收code

Route::get('/user/login','User\UserController@login');           //用户登录
Route::post('/user/login','User\UserController@doLogin');        //用户登录
Route::get('/user/center','User\UserController@center');        //个人中心


//模板引入静态文件
Route::get('/mvc/test1','Mvc\MvcController@test1');
Route::get('/mvc/bst','Mvc\MvcController@bst');



//Test
Route::get('/test/cookie1','Test\TestController@cookieTest1');
Route::get('/test/cookie2','Test\TestController@cookieTest2');
Route::get('/test/session','Test\TestController@sessionTest');
Route::get('/test/mid1','Test\TestController@mid1')->middleware('check.uid');        //中间件测试
Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');        //中间件测试


//购物车
//Route::get('/cart','Cart\IndexController@index')->middleware('check.uid');
Route::get('/cart','Cart\IndexController@index')->middleware('check.login.token');
Route::get('/cart/add/{goods_id}','Cart\IndexController@add')->middleware('check.login.token');      //添加商品
Route::get('/cart/del/{goods_id}','Cart\IndexController@del')->middleware('check.login.token');      //删除商品
Route::post('/cart/add2','Cart\IndexController@add2')->middleware('check.login.token');      //添加商品
Route::get('/cartindex','Cart\IndexController@index')->middleware('check.login.token');
Route::get('/cart/del2/{goods_id}','Cart\IndexController@del2')->middleware('check.login.token');

//商品
Route::get('/goods/{goods_id}','Goods\IndexController@index');                  //商品详情
Route::get('/goods','Goods\IndexController@indexList');                         //商品展示
Route::post('/goods/indexList2','Goods\IndexController@indexList2');          //商品搜索展示


//订单
Route::get('/order/add','Order\IndexController@add');                           //全部下单
Route::get('/order/list','Order\IndexController@list');                         //展示
Route::get('/order/del/{id}','Order\IndexController@del');                      //删除



//支付
Route::get('/pay/alipay/test','Pay\AlipayController@test');         //订单支付
Route::get('/pay/o/{id}','Pay\AlipayController@pay')->middleware('check.login.token');         //订单支付宝支付
Route::get('/weixin/o/{id}','Weixin\PayController@test');         //订单微信支付
Route::post('/pay/alipay/notify','Pay\AlipayController@aliNotify');        //支付宝支付 通知回调
Route::get('/pay/alipay/return','Pay\AlipayController@aliReturn');          //支付宝支付 同步通知回调



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::get('/upload','Goods\IndexController@uploadIndex');
Route::post('/goods/upload/pdf','Goods\IndexController@uploadPDF');

//微信
Route::get('/weixin/refresh_token','Weixin\WeixinController@refreshToken');     //刷新token
Route::get('/weixin/test/token','Weixin\WeixinController@test');
Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::post('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/create_menu','Weixin\WeixinController@createMenu');     //创建菜单


Route::get('/form/show','Weixin\WeixinController@formShow');     //表单测试
Route::post('/form/test','Weixin\WeixinController@formTest');     //表单测试




Route::get('/weixin/material/list','Weixin\WeixinController@materialList');     //获取永久素材列表
Route::get('/weixin/material/upload','Weixin\WeixinController@upMaterial');     //上传永久素材
Route::post('/weixin/material','Weixin\WeixinController@materialTest');     //创建菜单
//Route::post('/weixin/material','Weixin\WeixinController@materialTest');     //创建菜单

//微信聊天
Route::get('/weixin/kefu/chat','Weixin\WeixinController@chatView');     //客服聊天
Route::get('/weixin/chat/get_msg','Weixin\WeixinController@getChatMsg');     //获取用户聊天信息

//微信支付
Route::get('/weixin/pay/test','Weixin\PayController@test');     //微信支付测试
Route::post('/weixin/pay/notice','Weixin\PayController@notice');     //微信支付通知回调


//微信 JSSDK

Route::get('/weixin/jssdk/test','Weixin\WeixinController@jssdkTest');       // 测试