<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;

use App\Model\GoodsModel;

class GoodsController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('微信用户管理')
            ->description('用户列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new P_wx_userModel());

        $grid->model()->orderBy('id','desc');     //倒序排序
        $grid->add_time('添加时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->id('ID');
        $grid->headimgurl('微信头像')->display(function($img_url){
            return'<img src="'.$img_url.'">';
        });
        $grid->nickname('用户名');
        $grid->sex('性别');
        $grid->subscribe_time('subscribe_time')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });

        return $grid;
    }


    public function edit($id, Content $content)
    {

        echo __METHOD__;die;
        return $content
            ->header('微信用户管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    public function update($id)
    {
        echo '<pre>';print_r($_POST);echo '</pre>';
    }

    public function store()
    {
        echo '<pre>';print_r($_POST);echo '</pre>';
    }



    public function show($id)
    {
        echo __METHOD__;echo '</br>';
    }

    //删除
    public function destroy($id)
    {

        $response = [
            'status' => true,
            'message'   => 'ok'
        ];
        return $response;
    }

}
