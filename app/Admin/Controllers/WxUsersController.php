<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Grid;
use Encore\Admin\Form;

use App\Model\UsersModel;

class WxUsersController extends Controller
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
        $grid = new Grid(new UsersModel());

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


    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinUser);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->switch('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');

        return $form;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinUser::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');

        return $show;
    }

    /**
     * 消息群发
     */
    public function sendMsgView(Content $content)
    {
        //return view('admin.weixin.send_msg');

        return $content
            ->header('微信')
            ->description('群发消息')
            ->body(view('admin.weixin.send_msg'));
    }


    /**
     *
     */
    public function sendMsg()
    {
        //获取用户openid
        $list = WeixinUser::all()->pluck('openid')->take(10)->toArray();


        //群发消息

        echo '<pre>';print_r($list);echo '</pre>';
        echo '<pre>';print_r($_POST);echo '</pre>';
    }



}
