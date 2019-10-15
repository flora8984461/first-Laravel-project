<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StaticPagesController extends Controller
{
    public function home()
    {
        $feed_items = []; //我们定义了一个空数组 feed_items 来保存微博动态数据。
        if (Auth::check()) { //Auth::check() 来检查用户是否已登录
            $feed_items = Auth::user()->feed()->paginate(30);
        }
//在首页可以看到提取的微博
        return view('static_pages/home',compact('feed_items'));
    }

    public function help()
    {
        return view('static_pages/help');
    }

    public function about()
    {
        return view('static_pages/about');
    }
}
