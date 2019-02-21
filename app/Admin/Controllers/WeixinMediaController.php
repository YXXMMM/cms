<?php

namespace App\Admin\Controllers;

use App\Model\WeixinMedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WeixinMediaController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinMedia);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->add_time('Add time')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->msg_type('Msg type');
        $grid->media_id('Media id');
        $grid->msg_id('Msg id');
        $grid->local_file_name('Local file name')->display(function($img){
            if(substr($img,-3,3)=='mp4'){
                $imgs='<a href="/wx/video/'.$img.'">观看视频</a>';
            }elseif(substr($img,-3,3)=='amr'){
                $imgs='<a href="/wx/voice/'.$img.'">试听语音</a>';
            }else{
                $imgs='<img src="/wx/images/'.$img.'" width=80px;height=80px;> ';
            }
            return $imgs;
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinMedia::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->msg_type('Msg type');
        $show->media_id('Media id');
        $show->msg_id('Msg id');
        $show->local_file_name('Local file name');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinMedia);

        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('msg_type', 'Msg type');
        $form->text('media_id', 'Media id');
        $form->text('msg_id', 'Msg id');
        $form->text('local_file_name', 'Local file name');
        return $form;
    }
}