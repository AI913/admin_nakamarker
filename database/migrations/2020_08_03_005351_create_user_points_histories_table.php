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
            $table->bigIncrements('id')->comment('履歴ID');                                   // ID
            $table->unsignedInteger('type')->default('1')->comment('付与ポイントの種類');      // 付与ポイントの種類
            $table->unsignedInteger('give_point')->default(0)->comment('付与ポイント');       // 付与ポイント
            $table->unsignedInteger('pay_point')->default(0)->comment('消費ポイント');        // 消費ポイント
            $table->unsignedInteger('charge_flg')->default(1)->comment('有料フラグ');        // 有料フラグ
            $table->dateTime('limit_date')->nullable()->comment('有効期限日時');             // 有効期限日時
            $table->boolean('used_flg')->default(0)->comment('使用済みフラグ');              // 使用済みフラグ
            $table->unsignedInteger('to_user_id')->comment('ユーザID(受け手)');              // ユーザID(受け手)
            $table->unsignedInteger('from_user_id')->nullable()->comment('ユーザID(送り手)'); // ユーザID(送り手)
            $table->tinyInteger('status')->default(1)->comment('受け取り状況フラグ');         // 受け取り状況フラグ
            $table->text('memo')->nullable()->comment('備考');                              // 備考
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
        Schema::dropIfExists('user_points_histories');
    }
}
