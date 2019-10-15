<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;  //是自己才能够更新资料
    }

    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id; //是管理员且不是删除自己，才能够删除用户
    }

    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id; //不能是关注自己，只能关注别人
    }
}
