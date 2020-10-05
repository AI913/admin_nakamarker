<?php

use Illuminate\Database\Seeder;

class UserMarkerTableSeeder extends Seeder
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

        // 購入時の消費ポイントの値を配列にセット
        $price = [10, 20, 30, 50, 100];

        // レコード30件分出力
        for($i=0; $i < 30; $i++){
            \App\Model\UserMarker::create([
                'marker_id' => $faker->numberBetween(1, 30),
                'user_id' => $faker->numberBetween(1, 30),
                'pay_free_point' => $price[array_rand($price , 1 )],   // 購入時の消費ポイントの値をランダムに抽出
                'pay_charge_point' => $price[array_rand($price , 1 )], // 購入時の消費ポイントの値をランダムに抽出
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
