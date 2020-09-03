<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markers', function (Blueprint $table) {
            $table->increments('id');                           // ID
            $table->tinyInteger('type')->default(1);            // マーカーの種類
            $table->string('name');                             // マーカーの名前
            $table->string('description')->nullable();          // マーカーの説明
            $table->integer('price')->nullable()->default(0);   // マーカーにかかる消費ポイント数
            $table->tinyInteger('charge_flg')->default(1);      // マーカーの有料フラグ
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
        Schema::dropIfExists('markers');
    }
}
