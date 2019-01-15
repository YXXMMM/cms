{{-- 购物车 --}}
@extends('layouts.bst')

@section('content')
    <div class="container">
        <table class="table table-striped" >
            <tr>
                <td>商品名称</td>
                <td>价格</td>
                <td>添加时间</td>
                <td>操作</td>
            </tr>
            @foreach($list as $k=>$v)
                <tr>
                    <td>{{$v['goods_name']}}</td>
                    <td>¥ {{$v['price'] / 100}}</td>
                    <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                    <td>
                        <a href="/cart/del2/{{$v['goods_id']}}" class="del_goods">删除</a>
                    </td>
                </tr>
            @endforeach
        </table>
        <hr>
        <a href="/order/add" id="submit_order" class="btn btn-info "> 全部下单 </a>
    </div>
@endsection

@section('footer')
    @parent
@endsection