{{-- 订单展示 --}}
@extends('layouts.bst')
@section('content')
    <div class="container">
        <table class="table table-striped" >
            <tr>
                <td>订单号</td>
                <td>添加时间</td>
                <td>总金额</td>
                <td>操作</td>
            </tr>
            @foreach($list as $k=>$v)
                <tr>
                    <td>{{$v['order_sn']}}</td>
                    <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                    <td>{{$v['order_amount']/100}}</td>
                    <td><a href="#">去支付</a>||<a href="/order/del/{{$v['id']}}" class="del_goods">删除</a></td>
                </tr>
            @endforeach
        </table>
    </div>


@section('footer')
    @parent
@endsection