<?php

use Illuminate\Database\Seeder;

class UserPointsHistoryTableSeeder extends Seeder
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
            \App\Model\UserPointsHistory::create([
                'give_point'     => $faker->numberBetween(400, 1000),
                'pay_point'      => $faker->numberBetween(100, 500),
                'type'           => $faker->numberBetween(1, 2),
                'user_id'        => $faker->numberBetween(1, 15),
                'del_flg'        => 0,
                'update_user_id' => $faker->numberBetween(1, 15),
            ]);
        }
    }
}
