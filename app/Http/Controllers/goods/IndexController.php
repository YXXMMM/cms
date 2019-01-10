<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;

class IndexController extends Controller
{
    //


    /**
     * 商品详情
     * @param $goods_id
     */
    public function index($goods_id)
    {
        $goods = GoodsModel::where(['goods_id'=>$goods_id])->first();

        //商品不存在
        if(!$goods){
            header('Refresh:2;url=/user/center');
            echo '商品不存在,正在跳转至首页';
            exit;
        }

        $data = [
            'goods' => $goods
        ];
        return view('goods.index',$data);
    }


    /*
     * 商品展示
     * */
    public function indexList()
    {
        $goods = GoodsModel::get();

        $data = [
            'goods' => $goods
        ];
        //print_r ($data);die;

        return view('goods.indexlist',$data);
    }
}
