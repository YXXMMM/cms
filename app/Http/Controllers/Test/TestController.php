<?php

namespace App\Http\Controllers\Test;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use DB;
use Illuminate\Support\Facades\Session;

class TestController extends Controller
{
    //

    public function abc()
    {
        var_dump($_POST);echo '</br>';
        var_dump($_GET);echo '</br>';
    }

    public function world1()
    {
        echo __METHOD__;
    }


    public function hello2()
    {
        echo __METHOD__;
        header('Location:/world2');
    }

    public function world2()
    {
        header('Location:http://www.baidu.com');
    }

    public function md($m,$d)
    {
        echo 'm: '.$m;echo '<br>';
        echo 'd: '.$d;echo '<br>';
    }

    public function showName($name=null)
    {
        var_dump($name);
    }

    public function query1()
    {
        $list = DB::table('p_users')->get()->toArray();
        echo '<pre>';print_r($list);echo '</pre>';
    }

    public function query2()
    {
        $user = DB::table('p_users')->where('uid', 3)->first();
        echo '<pre>';print_r($user);echo '</pre>';echo '<hr>';
        $email = DB::table('p_users')->where('uid', 4)->value('email');
        var_dump($email);echo '<hr>';
        $info = DB::table('p_users')->pluck('age', 'name')->toArray();
        echo '<pre>';print_r($info);echo '</pre>';


    }


    public function viewTest1()
    {
        $data = [];
        return view('test.index',$data);
    }

    public function viewTest2()
    {
        $list = UserModel::all()->toArray();
        //echo '<pre>';print_r($list);echo '</pre>';

        $data = [
            'title'     => 'XXXX',
            'list'      => $list
        ];

        return view('test.child',$data);
    }

    /**
     * Cookie 测试
     * 2019年1月4日13:25:50
     */
    public function cookieTest1()
    {
        setcookie('cookie1','lening',time()+1200,'/','lening.com',false,true);
        echo '<pre>';print_r($_COOKIE);echo '</pre>';
    }

    public function cookieTest2()
    {
        echo '<pre>';print_r($_COOKIE);echo '</pre>';
    }

    public function sessionTest(Request $request)
    {
        echo '<pre>';print_r($request->session()->all());echo '</pre>';
        //echo '<pre>';print_r(Session::all());echo '</pre>';
    }
    public function mid1()
    {
        echo __METHOD__;
    }

    public function checkCookie()
    {
        echo __METHOD__;
    }
}
