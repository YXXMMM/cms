<?php

namespace App\Http\Controllers\Cart;

use App\Model\CartModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;

class IndexController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->uid = session()->get('uid');
            return $next($request);
        });
    }

    //
    public function index(Request $request)
    {
//        $goods = session()->get('cart_goods');
//        if(empty($goods)){
//            echo '购物车是空的';
//        }else{
//            foreach($goods as $k=>$v){
//                echo 'Goods ID: '.$v;echo '</br>';
//                $detail = GoodsModel::where(['goods_id'=>$v])->first()->toArray();
//                echo '<pre>';print_r($detail);echo '</pre>';
//            }
//        }
//        $uid = session()->get('uid');
//        echo 111;exit;
        $cart_goods = CartModel::where(['uid'=>$this->uid])->get()->toArray();

        if(empty($cart_goods)){
            header("Refresh:3;url=/goods");
            die("购物车是空的");
        }

        //echo '<pre>';print_r($cart_goods);echo '</pre>';echo '<hr>';
        if($cart_goods){
            //获取商品最新信息
            foreach($cart_goods as $k=>$v){
                $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                $goods_info['num']  = $v['num'];
                //echo '<pre>';print_r($goods_info);echo '</pre>';
                $list[] = $goods_info;
            }
        }

        $data = [
            'list'  => $list
        ];
        return view('cart.index',$data);

    }


    /**
     * 添加商品
     */
    public function add($goods_id)
    {

        $cart_goods = session()->get('cart_goods');

        //是否已在购物车中
        if(!empty($cart_goods)){
            if(in_array($goods_id,$cart_goods)){
                header("Refresh:3;url=/goods");
                echo '已存在购物车中';
                exit;
            }
        }

        session()->push('cart_goods',$goods_id);

        //减库存
        $where = ['goods_id'=>$goods_id];
        $store = GoodsModel::where($where)->value('store');

        if($store<=0){
            echo '库存不足';
            exit;
        }
        $rs = GoodsModel::where(['goods_id'=>$goods_id])->decrement('store');

        if($rs){
            echo '添加成功';
        }

    }

    /**
     * 购物车添加商品
     * @return array
     */
    public function add2(Request $request)
    {
        $goods_id = $request->input('goods_id');
        $num = $request->input('num');

        //检查库存
        $store_num = GoodsModel::where(['goods_id'=>$goods_id])->value('store');
        if($store_num<=0){
            $response = [
                'errno' => 5001,
                'msg'   => '库存不足'
            ];
            return $response;
        }

        //检查购物车重复商品
        $cart_goods = CartModel::where(['uid'=>$this->uid])->get()->toArray();
        if($cart_goods){
            $goods_id_arr = array_column($cart_goods,'goods_id');

            if(in_array($goods_id,$goods_id_arr)){
                $response = [
                    'errno' => 5002,
                    'msg'   => '商品已在购物车中，请勿重复添加'
                ];
                return $response;
            }
        }

        //写入购物车表
        $data = [
            'goods_id'  => $goods_id,
            'num'       => $num,
            'add_time'  => time(),
            'uid'       => $this->uid,
            'session_token' => session()->get('u_token')
        ];

        $cid = CartModel::insertGetId($data);
        if(!$cid){
            $response = [
                'errno' => 5002,
                'msg'   => '添加购物车失败，请重试'
            ];
            return $response;
        }


        $response = [
            'error' => 0,
            'msg'   => '添加成功'
        ];
        return $response;
    }

    /**
     * 删除商品
     */
    public function del($goods_id)
    {
        //判断 商品是否在 购物车中
        $goods = session()->get('cart_goods');
//        echo '<pre>';print_r($goods);echo '</pre>';

        if(in_array($goods_id,$goods)){
            //执行删除
            foreach($goods as $k=>$v){
                if($goods_id == $v){
                    session()->pull('cart_goods.'.$k);
                }
            }
        }else{
            header("Refresh:3;url=/cartindex");
            //不在购物车中
            die("商品不在购物车中");
        }

    }

    /*
     * 删除商品
     *
     * */
    public function del2($abc)
    {
        $rs = CartModel::where(['uid'=>$this->uid,'goods_id'=>$abc])->delete();
        //echo '商品ID:  '.$abc . ' 删除成功1';
        if($rs){
            echo '商品ID:  '.$abc . ' 删除成功1';
            header("Refresh:3;url=/cartindex");
        }else{
            echo '商品ID:  '.$abc . ' 删除成功2';
        }
    }






}
