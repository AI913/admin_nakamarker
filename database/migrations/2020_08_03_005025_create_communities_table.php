<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communities', function (Blueprint $table) {
            $table->increments('id');                           // ID
            $table->string('name');                             // コミュニティの名前
            $table->string('description')->nullable();          // コミュニティの説明
            $table->tinyInteger('status')->default(1);          // マーカーの公開フラグ
            $table->string('image_file')->nullable();           // 画像ファイル名
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
        Schema::dropIfExists('communities');
    }
}
