<?php

use Illuminate\Database\Seeder;

class MarkerTableSeeder extends Seeder
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

        // 購入価格ポイントの値を配列にセット
        $price = [30, 50, 100, 150, 200];

        // レコード30件分出力
        for($i=0; $i < 30; $i++){
            \App\Model\Marker::create([
                'type' => $faker->numberBetween(1, 3), // 1~3の間で乱数
                'name' => 'marker'.$i,
                'price' => $price[array_rand($price , 1 )],   // 購入価格ポイントの値をランダムに抽出
                'charge_flg' => $faker->numberBetween(1, 3), // 1~3の間で乱数
                'status' => 1,
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
