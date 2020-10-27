<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_locations', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ロケーションID');                    // ID
            $table->string('name')->comment('コミュニティの名前');                      // コミュニティの名前
            $table->string('latitude', '50')->comment('緯度');                        // 緯度
            $table->string('longitude', '50')->comment('経度');                       // 経度
            $table->string('image_file')->nullable()->comment('画像ファイル名');       // 画像ファイル名
            $table->text('memo')->nullable()->comment('備考');                        // 備考
            $table->unsignedInteger('user_id')->comment('ユーザID');                  // ユーザID
            $table->unsignedInteger('marker_id')->comment('マーカーID');              // マーカーID
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');        // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');             // 更新者ユーザID

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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_locations');
    }
}
