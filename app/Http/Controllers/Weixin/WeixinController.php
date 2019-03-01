<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WeixinChatModel;
use App\Model\WeixinUser;
use App\Model\WxUserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
use App\Model\WeixinMedia;
class WeixinController extends Controller
{
    //

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token

    public function test()
    {
        echo 'Token: ' . $this->getWXAccessToken();
    }

    /**
     * 首次接入
     */
    public function validToken1()
    {
        echo 1;
    }

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");

        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象
        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);
        $event = $xml->Event;                      //事件类型
        $openid = $xml->FromUserName;             //用户openid
        //var_dump($xml);echo '<hr>';
        // 处理用户发送消息

        if (isset($xml->MsgType)) {
            if ($xml->MsgType == 'text') {            //用户发送文本消息
                $msg = $xml->Content;
               $data=[
                   'text'=>$msg,
                   'add_time'=>time(),
                   'openid'=>$openid,
                   'nickname'=>$openid,
               ];
               $res=WeixinChatModel::insert($data);
            } elseif ($xml->MsgType == 'image') {       //用户发送图片信息
                //视业务需求是否需要下载保存图片
                if (1) {  //下载图片素材
                    $file_name = $this->dlWxImg($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . str_random(10) . ' >>> ' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                    echo $xml_response;
                    //写入数据库
                    $data = [
                        'openid' => $openid,
                        'add_time' => time(),
                        'msg_type' => 'image',
                        'media_id' => $xml->MediaId,
                        'format' => $xml->Format,
                        'msg_id' => $xml->MsgId,
                        'local_file_name' => $file_name
                    ];

                    $m_id = WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            } elseif ($xml->MsgType == 'voice') {        //处理语音信息
                $file_name = $this->dlVoice($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . str_random(10) . ' >>> ' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;
                //写入数据库
                $data = [
                    'openid' => $openid,
                    'add_time' => time(),
                    'msg_type' => 'voice',
                    'media_id' => $xml->MediaId,
                    'format' => $xml->Format,
                    'msg_id' => $xml->MsgId,
                    'local_file_name' => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
                var_dump($m_id);
            } elseif ($xml->MsgType == 'video') {        //处理视频消息
                $file_name = $this->dlVideo($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . str_random(10) . ' >>> ' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;
                //写入数据库
                $data = [
                    'openid' => $openid,
                    'add_time' => time(),
                    'msg_type' => 'video',
                    'media_id' => $xml->MediaId,
                    'format' => $xml->Format,
                    'msg_id' => $xml->MsgId,
                    'local_file_name' => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
                var_dump($m_id);
            } elseif ($xml->MsgType == 'event') {        //判断事件类型
                //exit();

                if ($event == 'subscribe') {
                    ;               //用户openid
                    $sub_time = $xml->CreateTime;               //扫码关注时间

                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);

                    //保存用户信息
                    $u = WeixinUser::where(['openid' => $openid])->first();
                    //var_dump($u);die;
                    if ($u) {       //用户不存在
                        //echo '用户已存在';
                    } else {
                        $user_data = [
                            'openid' => $openid,
                            'add_time' => time(),
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                        ];
                        $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                    }
                } elseif ($event == 'CLICK') {     //click  菜单
                    if ($xml->EventKey == 'kefu01') {
                        $this->kefu01($openid, $xml->ToUserName);
                    }

                }

            }
        }
    }

    /**
     * 客服处理
     * @param $openid   用户openid
     * @param $from     开发者公众号id 非 APPID
     */
    public function kefu01($openid, $from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $from . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . 'Hello World, 现在时间' . date('Y-m-d H:i:s') . ']]></Content></xml>';
        echo $xml_response;
    }

    /**
     * 下载图片素材
     * @param $media_id
     */
    public function dlWxImg($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->getWXAccessToken() . '&media_id=' . $media_id;

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0], '"'), -20);
        $wx_image_path = 'wx/images/' . $file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path, $response->getBody());
        if ($r) {     //保存成功

        } else {      //保存失败

        }
        return $file_name;
    }

    /**
     * 下载语音文件
     * @param $media_id
     */
    public function dlVoice($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->getWXAccessToken() . '&media_id=' . $media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0], '"'), -20);

        $wx_image_path = 'wx/voice/' . $file_name;
        //保存语音
        $r = Storage::disk('local')->put($wx_image_path, $response->getBody());
        if ($r) {     //保存成功

        } else {      //保存失败

        }
        return $file_name;
    }

    /**
     * 下载视频文件
     * @param $media_id
     */
    public function dlVideo($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->getWXAccessToken() . '&media_id=' . $media_id;

        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0], '"'), -20);

        $wx_image_path = 'wx/video/' . $file_name;
        //保存视频
        $r = Storage::disk('local')->put($wx_image_path, $response->getBody());
        if ($r) {     //保存成功

        } else {      //保存失败

        }
        return $file_name;
    }

    /**
     * 群发消息
     */
    public function sendTextAll()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=' . $this->getWXAccessToken();
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data = [
            "filter" => [
                "is_to_all" => true,
                "tag_id" => 2
            ],
            "text" => [
                "content" => "嗨皮"
            ],
            "msgtype" => "text"
        ];
        $r = $client->request('POST', $url, [
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);
        // 3 解析微信接口返回信息

        $response_arr = json_decode($r->getBody(), true);
        var_dump($response_arr);
        if ($response_arr['errcode'] == 0) {
            echo "群发成功";
        } else {
            echo "群发失败，请重试";
            echo '</br>';
            echo $response_arr['errmsg'];
        }
    }

    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
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

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';

        $data = json_decode(file_get_contents($url), true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }

    /*
     *创建服务号菜单
     */
    public function createMenu()
    {
        //1 获取access_token 拼接请求接口
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->getWXAccessToken();
        //2 请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $data = [
            "button" => [
                [
                    "name" => "秀歌",
                    "sub_button" => [
                        [
                            "type" => "click",      // click类型
                            "name" => "阴雨天",
                            "key" => "kefu01"
                        ]
                    ]
                ],
                [
                    "name" => "XXX",
                    "sub_button" => [
                        [
                            "type" => "view",      // view类型 跳转指定 URL
                            "name" => "BBY",
                            "url" => "http://yby.52self.cn"
                        ]
                    ]
                ]
            ]
        ];


        $r = $client->request('POST', $url, [
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);
        // 3 解析微信接口返回信息

        $response_arr = json_decode($r->getBody(), true);
        if ($response_arr['errcode'] == 0) {
            echo "菜单创建成功";
        } else {
            echo "菜单创建失败，请重试";
            echo '</br>';
            echo $response_arr['errmsg'];
        }
    }

    /**
     * 刷新access_token
     */
    public function refreshToken()
    {
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
    }


    public function materialTest()
    {
        //echo __METHOD__;echo '</br>';
        echo '<pre>';print_r($_POST);echo '</pre>';echo '</br>';
        echo '<pre>';print_r($_FILES);echo '</pre>';
    }

    /**
     * 上传素材
     */
    public function upMaterial()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'username',
                    'contents' => 'zhangsan'
                ],
                [
                    'name'     => 'media',
                    'contents' => fopen('abc.jpg', 'r')
                ],
            ]
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $d = json_decode($body,true);
        echo '<pre>';print_r($d);echo '</pre>';


    }



    public function upMaterialTest($file_path)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file_path, 'r')
                ],
            ]
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $d = json_decode($body,true);
        echo '<pre>';print_r($d);echo '</pre>';


    }


    /**
     * 获取永久素材列表
     */
    public function materialList()
    {
        $client = new GuzzleHttp\Client();
        $type = $_GET['type'];
        $offset = $_GET['offset'];

        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getWXAccessToken();

        $body = [
            "type"      => $type,
            "offset"    => $offset,
            "count"     => 20
        ];
        $response = $client->request('POST', $url, [
            'body' => json_encode($body)
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $arr = json_decode($response->getBody(),true);
        echo '<pre>';print_r($arr);echo '</pre>';


    }

    public function http405()
    {
        echo __METHOD__;
    }



    public function formShow()
    {

        return view('test.form');

    }

    public function formTest(Request $request)
    {
        //echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';
        //echo '<pre>';print_r($_FILES);echo '</pre>';echo '<hr>';

        //保存文件
        $img_file = $request->file('media');
        //echo '<pre>';print_r($img_file);echo '</pre>';echo '<hr>';

        $img_origin_name = $img_file->getClientOriginalName();
        echo 'originName: '.$img_origin_name;echo '</br>';
        $file_ext = $img_file->getClientOriginalExtension();          //获取文件扩展名
        echo 'ext: '.$file_ext;echo '</br>';

        //重命名
        $new_file_name = str_random(15). '.'.$file_ext;
        echo 'new_file_name: '.$new_file_name;echo '</br>';

        //文件保存路径


        //保存文件
        $save_file_path = $request->media->storeAs('form_test',$new_file_name);       //返回保存成功之后的文件路径

        echo 'save_file_path: '.$save_file_path;echo '<hr>';

        //上传至微信永久素材
        $this->upMaterialTest($save_file_path);


    }


    /**
     * 微信客服聊天
     */
    public function chatView()
    {
        $data = [
            'openid'    => 'oLreB1jAnJFzV_8AGWUZlfuaoQto'
        ];
        return view('weixin.chat',$data);
    }

    public function getChatMsg()
    {
        $openid = $_GET['openid'];  //用户openid
        $pos = $_GET['pos'];        //上次聊天位置
        $msg = WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->first();
        //$msg = WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->get();
        if($msg){
            $response = [
                'errno' => 0,
                'data'  => $msg->toArray()
            ];

        }else{
            $response = [
                'errno' => 50001,
                'msg'   => '服务器异常，请联系管理员'
            ];
        }

        die( json_encode($response));

    }

    /**
     * 微信登录测试
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login()
    {
//        echo urlencode('http://yxm.52self.cn/weixin/getcode');
        return view('weixin.login');
    }

    /**
     * 接收code
     */
    public function getCode(Request $request)
    {
//        echo '<pre>';print_r($_GET);echo '</pre>';
//        $code = $_GET['code'];
//        echo 'code: '.$code;
        $code = $_GET['code'];          // code
//        var_dump($code);exit;
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
//        var_dump($token_arr);exit;
        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];
        //   获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);
        $user_arr = json_decode($user_json,true);
        var_dump($user_arr);exit;
        //用户信息存入数据库
        $usersWhere=[
            'openid'=> $user_arr['openid'],
        ];
        $res=WxUserModel::where($usersWhere)->first();
        if($res) {
            echo 1;die;
            //用户已存在b
            $updatedate = [
                'openid' => $user_arr['openid'],
                'nickname' => $user_arr['nickname'],
                'sex' => $user_arr['sex'],
                'language' => $user_arr['language'],
                'headimgurl' => $user_arr['headimgurl'],
                'unionid' => $user_arr['unionid'],
                'uptime' => time()
            ];
            WxUserModel::where($usersWhere)->update($updatedate);
            $user_id=$res['id'];
            $request->session()->put('id',$user_id);
            header('refresh:2;url=/user/center');


        }else{
            echo 2;die;
            $WeixinDate=[
                'nickname'=>$user_arr['nickname'],
                'sex'=>$user_arr['sex'],
                'language'=>$user_arr['language'],
                'headimgurl'=>$user_arr['headimgurl'],
                'unionid'=>$user_arr['unionid'],
                'openid'=>$user_arr['openid'],
                'addtime'=>time()
            ];
            $user_id=WxUserModel::insertGetId($WeixinDate);
            $request->session()->put('id',$user_id);
            header('refresh:2;url=/user/center');

        }




    }
}