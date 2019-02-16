<?php

namespace App\Http\Controllers\Movie;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    //


    public function index()
    {

        $key = 'test_bit';      // redis key

        $seat_status = [];
        for($i=0;$i<=30;$i++){
            $status = Redis::getBit($key,$i);   //判断当前位 为0 或者 为1
            $seat_status[$i] = $status;
        }

        $data = [
            'seat'  => $seat_status
        ];
        return view('movie.index',$data);
    }

    /**
     * @param $pos  座位号
     * @param $status   0 | 1
     */
    public function buy($pos,$status)
    {
        $key = 'test_bit';

        Redis::setbit($key,$pos,$status);

    }
}
