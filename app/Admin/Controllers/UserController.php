<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;

use App\Model\UserModel;

class UsersController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new UserModel());

        $grid->uid('UID');
        $grid->nick_name('昵称');
        $grid->age('年龄');
        $grid->email('邮箱');
        $grid->reg_time('注册时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });

        return $grid;
    }


    public function edit($id)
    {
        echo __METHOD__;
    }



    //创建
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
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



    protected function form()
    {
        $form = new Form(new UserModel());

        $form->text('nick_name', '昵称');
        $form->text('age', '年龄');
        $form->email('email', 'Email');

        return $form;
    }
}
