<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth; //add to use Auth
use Mail; // add to use Mail

class UsersController extends Controller
{
    // signup users, 绑定users.create视图
    public function create()
    {
        return view('users.create');
    }

    //show user profile, 绑定users.show视图
    public function show(User $user)
    { //显示微博，按照created_at的时间descending排序，一页显示10条，传递在user.show视图上
        $statuses = $user->statuses()->orderBy('created_at', 'desc')->paginate(10);
        return view('users.show', compact('user','statuses'));
    }

    //signup and store user info , and login, 绑定users.show视图
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', 'Confirmation email has been sent to your email, please check, thank you!');
        return redirect('/');

        /*Auth::login($user); 替换掉了之前的注册完成后进入profile功能
        session()->flash('success', 'registered successfully');
        return redirect()->route('users.show', [$user]);*/
    }

    //edit user info, 绑定users.edit视图
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //update the new info to database, and redirect to user profile
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'required|confirmed|min:6'
        ]);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', 'updated successfully');

        return redirect()->route('users.show', $user);
    }

    //_Construct function,
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //index function, 提取出所有用户, 绑定users.index视图
    public function index()
    {
       // $users = User::all(); 显示所有
        $users = User::paginate(10); //分页
        return view('users.index', compact('users'));
    }

    //删除用户function
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', 'Successfully deleted!');
        return back();
    }

    //发送邮件的功能
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'summer@example.com';
        $name = 'Summer';
        $to = $user->email;
        $subject = "Thank you for registering!";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    //邮件激活注册功能
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', 'Congratulations! Successfully activated!');
        return redirect()->route('users.show', [$user]);
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(15);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(15);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
