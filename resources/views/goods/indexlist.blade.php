{{-- 商品展示 --}}
@extends('layouts.bst')
@section('content')
    <div class="container">
        <table class="table table-striped" >
            <tr>
                <td>商品名称</td>
                <td>商品库存</td>
                <td>添加时间</td>
                <td>操作</td>
            </tr>
            @foreach($goods as $k=>$v)
            <tr>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['store']}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td><a href="/goods/{{$v['goods_id']}}">详情</a></td>
            </tr>
            @endforeach
        </table>
    </div>


@section('footer')
    @parent
@endsection