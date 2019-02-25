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
use App\Model\WeixinChatModel;
use App\Model\WeixinUser;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class WxUsersController extends Controller
{
    protected $redis_weixin_access_token = 'str:weixin_access_token';
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
        $grid->actions(function ($actions) {
            // append一个操作
            $key=$actions->getKey();
            $actions->prepend('<a href="/admin/wxusers/create?user_id='.$key.'"><i class="fa fa-paper-plane"></i></a>');

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
        $user_id=$_GET['user_id'];
//        return $content
//            ->header('Create')
//            ->description('description')
//            ->body($this->form());
        $data=WeixinUser::where(['id'=>$user_id])->first();
        return $content
            ->header('Create')
            ->description('description')
            ->body(view('weixin.huiliao',['user_info'=>$data])->render());
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
    public function huiliao(Request $request)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();
        $openid=$request->input('openid');
        $weixin=$request->input('weixin');

        //print_r($url);
        //$content=$request->input('weixin');
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data = [
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=>[
                "content"=>$weixin
            ]
        ];
        // var_dump($data);
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);      //处理中文编码
        $r = $client->request('POST', $url, [
            'body' => $body
        ]);

        // 3 解析微信接口返回信息

        $response_arr = json_decode($r->getBody(), true);
        echo '<pre>';
        print_r($response_arr);
        echo '</pre>';

        if ($response_arr['errcode'] == 0) {
            //存入数据库
            $data=[
                'text'=>$weixin,
                'add_time'=>time(),
                'openid'=>$openid,
                'nickname'=>'客服'

            ];
            $res=WeixinChatModel::insert($data);
            $arr=[
                'code'=>0,
                'msg'=>'发送成功',
            ];
        }else{
            $arr=[
                'code'=>1,
                'msg'=>$response_arr['errmsg'],
            ];
        }
        echo json_encode($arr);
    }
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if (!$token) {        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . env('WEIXIN_APPID') . '&secret=' . env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url), true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token, $token);
            Redis::setTimeout($this->redis_weixin_access_token, 3600);
        }
        return $token;

    }
    public function wx_huiliao(Request $request)
    {
        $openid=$request->input('openid');
        $new=WeixinChatModel::orderBy('add_time','asc')->where(['openid'=>$openid])->get();
        echo json_encode($new);
    }

}
