<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make(); // 创建50个假用户
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray()); //insert插入数据库

        $user = User::find(1);
        $user->name = 'Summer';
        $user->email = 'summer@example.com';
        $user->password = '12345';
        $user->is_admin = true;
        $user->save();
    }
}
