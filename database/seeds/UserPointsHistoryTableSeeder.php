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

        // ポイントの値を配列にセット
        $point = [100, 150, 200, 250, 300, 500, 700, 1000];

        // レコード30件分出力
        for($i=0; $i < 30; $i++){
            \App\Model\UserPointsHistory::create([
                'give_point'     => $point[array_rand($point , 1 )],   // 付与ポイントの値をランダムに抽出
                'pay_point'      => $point[array_rand($point , 1 )],   // 消費ポイントの値をランダムに抽出
                'charge_flg'     => $faker->numberBetween(1, 2),
                'type'           => $faker->numberBetween(1, 4),
                'user_id'        => $faker->numberBetween(1, 30),
                'memo'           => 'テストメモ'.$i,
                'del_flg'        => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
