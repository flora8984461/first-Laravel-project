<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth; //add Auth to use

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials,$request->has('remember'))) {
            // 登录成功后的相关操作
            if(Auth::user()->activated) { // 如果激活了注册邮箱
            session()->flash('success', 'welcome back!');
            $fallback = route('users.show', Auth::user());
            return redirect()->intended($fallback);}
            else{ //如果没激活注册邮件
                Auth::logout();
                session()->flash('warning', 'Your account is not activated, please check your email');
                return redirect('/');
            }
        } else {
            // 登录失败后的相关操作
            session()->flash('danger', 'sorry, email does not match your password');
            return redirect()->back()->withInput();
        }

    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success', 'Goodbye！');
        return redirect('login');
    }

    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
}
