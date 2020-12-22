<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityMarkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_markers', function (Blueprint $table) {
            $table->increments('id');                                               // ID
            $table->unsignedInteger('marker_id')->comment('マーカーID');            // マーカーID
            $table->unsignedInteger('community_id')->comment('コミュニティID');     // コミュニティID
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');      // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');           // 更新者ユーザID

            $table->timestamps();

            // 外部キー制約
            $table->foreign('marker_id')
                  ->references('id')
                  ->on('markers')
                  ->onDelete('cascade');

            $table->foreign('community_id')
                  ->references('id')
                  ->on('communities')
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
        Schema::dropIfExists('community_markers');
    }
}
