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
            $table->increments('id')->comment('ID');                                        // ID
            $table->tinyInteger('type')->default(1)->comment('種別');                       // 種別
            $table->string('name')->comment('コミュニティの名前');                           // コミュニティの名前
            $table->string('description')->nullable()->comment('コミュニティの説明');        // コミュニティの説明
            $table->tinyInteger('status')->default(1)->comment('マーカーの公開フラグ');      // マーカーの公開フラグ
            $table->string('image_file')->nullable()->comment('画像ファイル名');            // 画像ファイル名
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');              // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');                   // 更新者ユーザID

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
