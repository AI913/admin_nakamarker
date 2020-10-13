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
            $table->bigIncrements('id');                            // ID
            $table->unsignedInteger('type')->default('1');          // 付与ポイントの種類
            $table->unsignedInteger('give_point')->default(0);      // 付与ポイント
            $table->unsignedInteger('pay_point')->default(0);       // 消費ポイント
            $table->unsignedInteger('charge_flg')->default(1);      // 有料フラグ
            $table->dateTime('limit_date')->nullable();             // 有効期限日時
            $table->boolean('used_flg')->default(0);                // 使用済みフラグ
            $table->unsignedInteger('to_user_id');                  // ユーザID(受け手)
            $table->unsignedInteger('from_user_id')->nullable();    // ユーザID(送り手)
            $table->tinyInteger('status')->default(1);              // 受け取り状況フラグ
            $table->text('memo')->nullable();                       // 備考
            $table->tinyInteger('del_flg')->default(0);             // 削除フラグ
            $table->integer('update_user_id');                      // 更新者ユーザID
            
            $table->timestamps();

            // 外部キー制約
            $table->foreign('to_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('from_user_id')
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
