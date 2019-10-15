<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Status;

class StatusPolicy
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
//因为之前我们已经在 AuthServiceProvider 中设置了「授权策略自动注册」，所以这里不需要做任何处理 StatusPolicy 将会被自动识别。
//删除授权，对应的user只能删除自己的微博
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }
}
