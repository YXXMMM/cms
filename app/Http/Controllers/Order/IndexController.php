<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\OrderModel;

class IndexController extends Controller
{
    //

    public function index()
    {
        echo __METHOD__;
    }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->uid = session()->get('uid');
            return $next($request);
        });
    }

    /**
     * 全部下单
     */
    public function add(Request $request)
    {
        //查询购物车商品
        $cart_goods = CartModel::where(['uid'=>$this->uid])->orderBy('id','desc')->get()->toArray();
        if(empty($cart_goods)){
            header("Refresh:3;url=/goods");
            die("购物车中无商品");
        }
        $order_amount = 0;
        foreach($cart_goods as $k=>$v){
            $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goods_info['num'] = $v['num'];
            $list[] = $goods_info;

            //计算订单价格 = 商品数量 * 单价
            $order_amount += $goods_info['price'] * $v['num'];
        }

        //生成订单号
        $order_sn = OrderModel::generateOrderSN();
        echo $order_sn;
        $data = [
            'order_sn'      => $order_sn,
            'uid'           => $this->uid,
            'add_time'      => time(),
            'order_amount'  => $order_amount
        ];

        $oid = OrderModel::insertGetId($data);
        if(!$oid){
            echo '生成订单失败';
        }

        echo '下单成功,订单号：'.$oid .' 跳转支付';
        header("Refresh:3;url=/order/list");

        //清空购物车
        CartModel::where(['uid'=>$this->uid])->delete();
    }

    /*
     * 订单展示
     * */
    public function list(Request $request){
        $order_goods = OrderModel::where(['uid'=>$this->uid,'is_pay'=>0])->get()->toArray();

        if(empty($order_goods)){
            die("订单表是空的");
        }

        //echo '<pre>';print_r($cart_goods);echo '</pre>';echo '<hr>';
        $list = OrderModel::where(['uid'=>$this->uid,'is_pay'=>0])->get();

        $data = [
            'list'  => $list
        ];
        return view('order.list',$data);
    }

    /*
     * 订单删除
     * */
    public function del($abc){
        $rs = OrderModel::where(['uid'=>$this->uid,'id'=>$abc])->delete();
        //echo '商品ID:  '.$abc . ' 删除成功1';
        if($rs){
            echo '订单ID:  '.$abc . ' 删除成功1';
            header("Refresh:3;url=/order/list");
        }else{
            echo '订单ID:  '.$abc . ' 删除成功2';
        }
    }

}
