<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');                       // ID
            $table->string('key');                          // システム設定に利用出来る共通キー 
            $table->string('value');                        // システム設定に利用出来る共通バリュー
            $table->text('memo')->nullable();               // 備考
            $table->tinyInteger('del_flg')->default(0);     // 削除フラグ
            $table->integer('update_user_id');              // 更新者ユーザID

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
        Schema::dropIfExists('configs');
    }
}
