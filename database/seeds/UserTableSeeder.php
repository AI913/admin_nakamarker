<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // faker使う(引数には日本語を設定している)
        $faker = Faker\Factory::create('ja_JP');

        // レコード15件分出力
        for($i=0; $i < 15; $i++){
            \App\Model\User::create([
                'name' => 'test'.$i,
                'email' => 'test'.$i.'@nakamarker.co.jp',
                'password' => Hash::make("test"),
                'device_token' => 'test'.(string)$i,
                'status' => 1,
                'user_agent' => $faker->userAgent,
                'del_flg' => 0,
                'update_user_id' => $i + 1,
            ]);
        }
    }
}
