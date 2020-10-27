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
            $table->increments('id')->comment('ID');                                                               // ID
            $table->tinyInteger('type')->default(1)->comment('マーカーの種類');                                     // マーカーの種類
            $table->string('name')->comment('マーカーの名前');                                                      // マーカーの名前
            $table->string('description')->nullable()->comment('マーカーの説明');                                   // マーカーの説明
            $table->unsignedInteger('price')->nullable()->default(0)->comment('マーカー価格(ポイント数)');          // マーカー価格(ポイント数)
            $table->tinyInteger('charge_flg')->default(1)->comment('マーカーの有料フラグ');                         // マーカーの有料フラグ
            $table->tinyInteger('status')->default(1)->comment('マーカーの公開フラグ');                             // マーカーの公開フラグ
            $table->string('image_file')->nullable()->comment('画像ファイル名');                                    // 画像ファイル名
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
        Schema::dropIfExists('markers');
    }
}
