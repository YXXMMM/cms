{{-- 用户登录--}}

@extends('layouts.bst')

@section('content')
    <form class="form-signin" action="/user/login" method="post">
        {{csrf_field()}}
        <h2 class="form-signin-heading">请登录</h2>
        <label for="inputEmail">Email</label>
        <input type="email" name="u_email" id="inputEmail" class="form-control" placeholder="@" required autofocus>
        <label for="inputPassword" >Password</label>
        <input type="password" name="u_pass" id="inputPassword" class="form-control" placeholder="***" required>
        <div class="checkbox">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
@endsection




