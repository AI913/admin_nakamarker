<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_histories', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('履歴ID');                                         // ID
            $table->unsignedInteger('community_id')->comment('コミュニティID');                      // コミュニティID
            $table->unsignedInteger('user_id')->comment('ユーザID');                                // ユーザID
            $table->tinyInteger('status')->default(1)->comment('コミュニティ申請の状態フラグ');       // コミュニティ申請の状態フラグ
            $table->text('memo')->nullable()->comment('備考');                                      // 備考
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');                      // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');                           // 更新者ユーザID

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
        Schema::dropIfExists('community_histories');
    }
}
