<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');                            // ユーザーID
            $table->string('name')->nullable();                  // ユーザ名
            $table->string('email')->unique();                   // メールアドレス
            $table->timestamp('email_verified_at')->nullable();  // メールアドレス確認カラム
            $table->string('password');                          // パスワード
            $table->rememberToken()->nullable();                 // トークン
            $table->string('user_token', '100')->nullable();     // ユーザトークン
            $table->datetime('login_time')->nullable();          // 最終ログイン日時
            $table->string('device_token')->nullable();          // デバイストークン
            $table->tinyInteger('status')->default(1);           // ステータス
            $table->string('user_agent')->nullable();            // ユーザエージェント
            $table->text('memo')->nullable();                    // 備考
            $table->tinyInteger('del_flg')->default(0);          // 削除フラグ
            $table->integer('update_user_id');                   // 更新者ユーザID

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
        Schema::dropIfExists('users');
    }
}
