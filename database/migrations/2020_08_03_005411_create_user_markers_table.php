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
            $table->increments('id')->comment('履歴ID');                                                            // ID
            $table->unsignedInteger('marker_id')->comment('マーカーID');                                            // マーカーID
            $table->unsignedInteger('user_id')->comment('ユーザID');                                                // ユーザID
            $table->unsignedInteger('pay_free_point')->default(0)->comment('マーカー購入時の消費ポイント(無料)');      // マーカー購入時の消費ポイント(無料)
            $table->unsignedInteger('pay_charge_point')->default(0)->comment('マーカー購入時の消費ポイント(有料)');    // マーカー購入時の消費ポイント(有料)
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
        Schema::dropIfExists('user_markers');
    }
}
