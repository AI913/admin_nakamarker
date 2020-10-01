<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_histories', function (Blueprint $table) {
            $table->increments('id');                           // ID
            $table->string('title');                            // タイトル
            $table->string('content');                          // 本文
            $table->integer('type')->default(1);                // 送信種別
            $table->text('option')->nullable();                 // 送信対象者条件
            $table->integer('send_count')->nullable();          // 送信カウント
            $table->dateTime('reservation_date');               // 送信予約日時
            // $table->dateTime('send_date');                      // 送信日時
            $table->tinyInteger('status')->default(1);          // 送信ステータス
            $table->text('memo')->nullable();                   // 備考
            $table->tinyInteger('del_flg')->default(0);         // 削除フラグ
            $table->integer('update_user_id');                  // 更新者ユーザID

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
        Schema::dropIfExists('push_histories');
    }
}
