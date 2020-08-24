<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_locations', function (Blueprint $table) {
            $table->bigIncrements('id');                    // ID
            $table->string('name');                         // コミュニティの名前
            $table->string('latitude', '50');               // 緯度
            $table->string('longitude', '50');              // 経度
            $table->string('image_file')->nullable();       // 画像ファイル名
            $table->text('memo')->nullable();               // 備考
            $table->unsignedInteger('user_id');             // ユーザID
            $table->unsignedInteger('marker_id');           // マーカーID
            $table->unsignedInteger('community_id');        // コミュニティID
            $table->tinyInteger('del_flg')->default(0);     // 削除フラグ
            $table->integer('update_user_id');              // 更新者ユーザID

            $table->timestamps();

            // 外部キー制約
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('community_locations');
    }
}
