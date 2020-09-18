<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_markers', function (Blueprint $table) {
            $table->increments('id');                           // ID
            $table->unsignedInteger('marker_id');               // マーカーID
            $table->unsignedInteger('user_id');                 // ユーザID
            $table->unsignedInteger('pay_point')->default(0);   // マーカー購入時の消費ポイント
            $table->tinyInteger('del_flg')->default(0);         // 削除フラグ
            $table->integer('update_user_id');                  // 更新者ユーザID

            $table->timestamps();

            // 外部キー制約
            $table->foreign('marker_id')
                  ->references('id')
                  ->on('markers')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_markers');
    }
}
