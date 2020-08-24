<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPointsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_points_histories', function (Blueprint $table) {
            $table->bigIncrements('id');                    // ID
            $table->integer('give_point');                  // 付与ポイント
            $table->integer('pay_point');                   // 消費ポイント
            $table->dateTime('limit_date')->nullable();     // 有効期限日時
            $table->unsignedInteger('user_id');             // ユーザID
            $table->tinyInteger('del_flg')->default(0);     // 削除フラグ
            $table->integer('update_user_id');              // 更新者ユーザID
            
            $table->timestamps();

            // 外部キー制約
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
        Schema::dropIfExists('user_points_histories');
    }
}
