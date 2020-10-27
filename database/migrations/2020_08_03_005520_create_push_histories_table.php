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
            $table->increments('id')->comment('ID');                                // ID
            $table->string('title')->comment('タイトル');                            // タイトル
            $table->string('content')->comment('本文');                              // 本文
            $table->integer('type')->default(1)->comment('送信種別');                // 送信種別
            $table->text('option')->nullable()->comment('送信対象者条件');           // 送信対象者条件
            $table->integer('send_count')->nullable()->comment('送信カウント');      // 送信カウント
            $table->dateTime('reservation_date')->comment('送信予約日時');           // 送信予約日時
            $table->tinyInteger('status')->default(1)->comment('送信ステータス');    // 送信ステータス
            $table->text('memo')->nullable()->comment('備考');                      // 備考
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');       // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');            // 更新者ユーザID

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
