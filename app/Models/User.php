<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //生成令牌
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }

    //头像
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = 'users';

    //一个用户可以有多个微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //该方法将当前用户发布过的所有微博从数据库中取出，并根据创建时间来倒序排序。
    public function feed()
    {
      /*  return $this->statuses()
            ->orderBy('created_at', 'desc'); */

      //$user->followings 与 $user->followings() 调用时返回的数据是不一样的， $user->followings 返回的是 Eloquent：集合 。
        //而 $user->followings() 返回的是 数据库请求构建器，可以简单理解为 followings 返回的是数据集合，而 followings() 返回的是数据库查询语句
        //如果用$user->followings(), 应该是 $user->followings()->get() 或者 $user->followings()->paginate()才能获取到最终数据

        $user_ids = $this->followings->pluck('id')->toArray(); //通过 followings 方法取出所有关注用户的信息，再借助 pluck 方法将 id 进行分离并赋值给 user_ids

        array_push($user_ids, $this->id); //将当前用户的 id 加入到 user_ids 数组中

        return Status::whereIn('user_id', $user_ids) //使用 Laravel 提供的 查询构造器 whereIn 方法取出所有用户的微博动态并进行倒序排序
            ->with('user') //使用了 Eloquent 关联的 预加载 with 方法，预加载避免了 N+1 查找的问题，大大提高了查询效率
            ->orderBy('created_at', 'desc');
    }
    //如果使用 get() 方法的话：$user->followings == $user->followings()->get() // 等于 true


    //belongsToMany 关联模型之间的多对多关系
    public function followers()
    {
        //table:followers, 把关联表取名为followers，传递额外参数 user_id 和 follower_id
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }


    //使用 attach 方法或 sync 方法在中间表上创建一个多对多记录，使用 detach 方法在中间表上移除一个记录，创建和移除操作并不会影响到两个模型各自的数据，所有的数据变动都在 中间表 上进行。
    //但是 attach 方法在我们对同一个 id 进行添加时，则会出现 id 重复的情况
    //sync方法不会重复，这里采用sync方法
    public function follow($user_ids)
    {
        if ( ! is_array($user_ids)) { //is_array 用于判断参数是否为数组，如果已经是数组，则没有必要再使用 compact 方法。
            $user_ids = compact('user_ids');
        }

        //sync 方法会接收两个参数，第一个参数为要进行添加的 id，第二个参数则指明是否要移除其它不包含在关联的 id 数组中的 id，true 表示移除，false 表示不移除，默认值为 true。
        //由于我们在关注一个新用户的时候，仍然要保持之前已关注用户的关注关系，因此不能对其进行移除，所以在这里我们选用 false
        $this->followings()->sync($user_ids, false);
    }

    //我们并没有给 sync 和 detach 指定传递参数为用户的 id，这两个方法会自动获取数组中的 id。

    public function unfollow($user_ids)
    {
        if ( ! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //判断当前登录的用户 A 是否关注了用户 B，即判断用户 B 是否包含在用户 A 的关注人列表上。
    //这里我们将用到 contains 方法来做判断。
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
