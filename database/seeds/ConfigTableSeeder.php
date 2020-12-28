<?php

use Illuminate\Database\Seeder;

class ConfigTableSeeder extends Seeder
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

        \App\Model\Config::create([
            'key' => 'password_limit_date',
            'value' => 1,
            'del_flg' => 0,
            'update_user_id' => $faker->numberBetween(1, 5),
        ]);
        \App\Model\Config::create([
            'key' => 'news_list',
            'value' => null,
            'del_flg' => 0,
            'update_user_id' => $faker->numberBetween(1, 5),
        ]);

        // レコード15件分出力
        for($i=0; $i < 30; $i++){
            \App\Model\Config::create([
                'key' => 'key'.$i,
                'value' => $faker->words($nb = 1, $asText = true),
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
