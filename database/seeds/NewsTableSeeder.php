<?php

use Illuminate\Database\Seeder;

class NewsTableSeeder extends Seeder
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
            \App\Model\News::create([
                'type' => $faker->numberBetween(1, 2), // 1~2の間で乱数
                'title' => 'News'.$i,
                'body' => $faker->sentences($nb = 3, $asText = true),
                'status' => 1,
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 15),
            ]);
        }
    }
}
