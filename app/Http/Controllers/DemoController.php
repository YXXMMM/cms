<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DemoController extends Controller
{
    public function demo(Request $request){

        $data=UserModel::where('id',61)->first()->toArray();

        dd($data);

        return view( 'demo') ;

    }
}