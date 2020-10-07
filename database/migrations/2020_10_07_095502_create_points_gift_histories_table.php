<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsGiftHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points_gift_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('give_point')->default(0);       // 付与ポイント
            $table->unsignedInteger('charge_flg')->default(1);       // 有料フラグ
            $table->unsignedInteger('give_user_id');                 // ギフトする側のユーザID
            $table->unsignedInteger('take_user_id');                 // 受け取る側のユーザID
            $table->tinyInteger('status')->default(1);               // 受け取り状況フラグ
            $table->unsignedBigInteger('user_points_history_id');    // user_points_historiesテーブルのID
            $table->text('memo')->nullable();                        // 備考
            $table->tinyInteger('del_flg')->default(0);              // 削除フラグ
            $table->integer('update_user_id');                       // 更新者ユーザID

            $table->timestamps();

            // 外部キー制約
            $table->foreign('give_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('take_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('user_points_history_id')
                  ->references('id')
                  ->on('user_points_histories')
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
        Schema::dropIfExists('points_gift_histories');
    }
}
