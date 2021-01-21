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
            $table->increments('id')->comment('ID');                                                                // ユーザーID
            $table->string('name')->nullable()->comment('ユーザ名');                                                // ユーザ名
            $table->string('user_token', '100')->nullable()->comment('ユーザトークン(アクセストークンとして利用)');    // ユーザトークン(アクセストークンとして利用)
            $table->string('firebase_uid')->nullable()->comment('Firebaseが発行するトークン');                       // UID(コミュニティ加入時に登録必須データ)
            $table->string('email')->nullable()->unique()->comment('メールアドレス');                               // メールアドレス
            $table->timestamp('email_verified_at')->nullable()->comment('メールアドレス確認カラム');                 // メールアドレス確認カラム
            $table->string('password')->comment('パスワード')->nullable();                                          // パスワード
            $table->rememberToken()->nullable()->comment('トークン');                                               // トークン
            $table->dateTime('login_time')->nullable()->comment('最終ログイン日時');                                 // 最終ログイン日時
            $table->string('device_token')->nullable()->comment('デバイストークン');                                 // デバイストークン
            $table->tinyInteger('status')->default(1)->comment('ステータス');                                       // ステータス
            $table->string('user_agent')->nullable()->comment('ユーザエージェント');                                 // ユーザエージェント
            $table->string('onetime_password')->nullable()->comment('ワンタイムパスワード');                         // ワンタイムパスワード
            $table->dateTime('limit_date')->nullable()->comment('ワンタイムパスワードの有効期限');                    // ワンタイムパスワードの有効期限
            $table->string('image_file')->nullable()->comment('画像ファイル名');                                     // 画像ファイル名
            $table->text('memo')->nullable()->comment('備考');                                                      // 備考
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');                                      // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');                                           // 更新者ユーザID

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
