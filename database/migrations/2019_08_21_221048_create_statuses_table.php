<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('content');
            $table->integer('user_id')->index(); //给user_id加索引，方便找和排序
            //timestamps()自带为微博数据表生成一个微博创建时间字段 created_at 和一个微博更新时间字段 updated_at，因此在这里我们并不需要再手动创建；这里给它加上索引方便查找排序
            $table->index(['created_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
