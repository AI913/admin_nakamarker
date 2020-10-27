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
            $table->increments('id')->comment('ID');                                        // ID
            $table->string('title')->comment('ニュースタイトル');                             // ニュースタイトル
            $table->text('body')->comment('ニュース本文');                                    // ニュース本文
            $table->string('image_file')->nullable()->comment('画像ファイル名');              // 画像ファイル名
            $table->dateTime('condition_start_time')->nullable()->comment('公開開始日時');    // 公開開始日時
            $table->dateTime('condition_end_time')->nullable()->comment('公開終了日時');      // 公開終了日時
            $table->tinyInteger('status')->default(1)->comment('公開フラグ');                 // 公開フラグ
            $table->text('memo')->nullable()->comment('備考');                               // 備考
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');                // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');                     // 更新者ユーザID

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
