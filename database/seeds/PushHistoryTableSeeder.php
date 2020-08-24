<?php

use Illuminate\Database\Seeder;

class PushHistoryTableSeeder extends Seeder
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
            \App\Model\PushHistory::create([
                'message' => $faker->sentences($nb = 1, $asText = true),
                'type' => 1,
                'send_date' => $faker->dateTimeThisYear(),
                'status' => 1,
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 15),
            ]);
        }
    }
}
