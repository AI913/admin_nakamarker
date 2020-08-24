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
            $table->bigIncrements('id');                    // ID
            $table->unsignedInteger('community_id');        // コミュニティID
            $table->unsignedInteger('user_id');             // ユーザID
            $table->tinyInteger('status')->default(1);      // コミュニティ申請の状態フラグ
            $table->text('memo')->nullable();               // 備考
            $table->tinyInteger('del_flg')->default(0);     // 削除フラグ
            $table->integer('update_user_id');              // 更新者ユーザID

            $table->timestamps();

            // 外部キー制約
            $table->foreign('community_id')
            ->references('id')
            ->on('communities')
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
        Schema::dropIfExists('community_histories');
    }
}
