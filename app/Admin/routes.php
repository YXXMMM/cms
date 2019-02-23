<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('/goods',GoodsController::class);
    $router->resource('/users',UsersController::class);
    $router->resource('/wxusers',WxUsersController::class);//微信用户管理
    $router->resource('/media',WeixinMediaController::class);//素材管理
    $router->resource('/groupsend',WeixinPerpetualController::class);//群发

    $router->get('/weixin/sendmsg','WeixinController@form');
    $router->post('/weixin/sendmsg','WeixinController@form');

});
