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
            $table->increments('id')->comment('ID');                                            // ID
            $table->string('key')->nullable()->comment('システム設定に利用出来る共通キー');        // システム設定に利用出来る共通キー 
            $table->string('value')->nullable()->comment('システム設定に利用出来る共通バリュー');  // システム設定に利用出来る共通バリュー
            $table->text('memo')->nullable()->comment('備考');                                  // 備考
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ');                  // 削除フラグ
            $table->integer('update_user_id')->comment('更新者ユーザID');                       // 更新者ユーザID

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
