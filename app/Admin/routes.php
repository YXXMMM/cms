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
    $router->post('/groupsend','WeixinPerpetualController@sendTextAll');//群发
    $router->resource('/tp',WeixinTpController::class);//群发
    $router->post('/tp','WeixinTpController@formTest');

    $router->post('/wxusers/hui','WxUsersController@huiliao');
    $router->post('/wxusers/liao','WxUsersController@wx_huiliao');
    $router->get('/wxusers/create?user_id=($user_id)','WxUsersController@create');


});
