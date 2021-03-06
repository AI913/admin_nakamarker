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
            $table->bigIncrements('id')->comment('ロケーションID');                          // ID
            $table->string('name')->comment('ロケーションの名前');                           // ロケーションの名前
            $table->string('latitude', '50')->comment('緯度');                              // 緯度
            $table->string('longitude', '50')->comment('経度');                             // 経度
            $table->string('image_file')->nullable()->comment('画像ファイル名(1枚目)');      // 画像ファイル名(1枚目)
            $table->string('image_file2')->nullable()->comment('画像ファイル名(2枚目)');     // 画像ファイル名(2枚目)
            $table->string('image_file3')->nullable()->comment('画像ファイル名(3枚目)');     // 画像ファイル名(3枚目)
            $table->text('memo')->nullable()->comment('備考');                              // 備考
            $table->unsignedInteger('user_id')->comment('ユーザID');                        // ユーザID
            $table->unsignedInteger('marker_id')->comment('マーカーID');                    // マーカーID
            $table->unsignedInteger('community_id')->comment('コミュニティID');             // コミュニティID
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
        Schema::dropIfExists('community_locations');
    }
}
