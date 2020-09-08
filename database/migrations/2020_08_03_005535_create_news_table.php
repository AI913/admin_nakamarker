<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');                                   // ID
            $table->string('title');                                    // ニュースタイトル
            $table->text('body');                                       // ニュース本文
            $table->string('image_file')->nullable();                   // 画像ファイル名
            $table->dateTime('condition_start_time')->nullable();       // 公開開始日時
            $table->dateTime('condition_end_time')->nullable();         // 公開終了日時
            $table->tinyInteger('status')->default(1);                  // 公開フラグ
            $table->text('memo')->nullable();                           // 備考
            $table->tinyInteger('del_flg')->default(0);                 // 削除フラグ
            $table->integer('update_user_id');                          // 更新者ユーザID

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
        Schema::dropIfExists('news');
    }
}
