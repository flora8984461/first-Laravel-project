<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //创建微博
    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required|max:140'  //限制微博内容字数不超过140
        ]);

        //Auth::user() - the currently authenticated user
        Auth::user()->statuses()->create([  //当前的user发的对应的微博 ($user->statuses()->create(), 这里的$user是Auth::user())
            'content' => $request['content'] //对content赋值
        ]);
        session()->flash('success', 'successfully released your Weibo!');
        return redirect()->back();
    }

    //删除微博
    public function destroy(Status $status) //这里我们使用的是『隐性路由模型绑定』功能，Laravel 会自动查找并注入对应 ID 的实例对象 $status，如果找不到就会抛出异常。
    {
        $this->authorize('destroy', $status); //做删除授权的检测，不通过会抛出 403 异常。
        $status->delete(); //调用 Eloquent 模型的 delete 方法对该微博进行删除。
        session()->flash('success', 'This Weibo is successfully deleted!'); //删除成功后闪存一个session, 出现提示信息
        //删除成功之后，将返回到执行删除微博操作的页面上。
        return redirect()->back();
    }
}
