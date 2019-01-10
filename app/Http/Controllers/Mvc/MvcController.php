<?php

namespace App\Http\Controllers\Mvc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MvcController extends Controller
{
    //

    public function test1()
    {
        $data = [
            'title' => 'MVC-Test'
        ];
        return view('mvc.index',$data);
    }

    public function bst()
    {
        $data = [
            'title' => 'MVC-Test'
        ];
        return view('mvc.bst',$data);
    }
}
