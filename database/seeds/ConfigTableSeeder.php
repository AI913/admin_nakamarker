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
            'memo'  => 'ニュースデータの表示件数を設定  例）15件表示する場合、"15"を設定',
            'del_flg' => 0,
            'update_user_id' => $faker->numberBetween(1, 5),
        ]);
        \App\Model\Config::create([
            'key' => 'reset_hour',
            'value' => 0,
            'memo'  => 'ログイン日時の日付変更基準時間を設定  例）13時にする場合、"13"を設定',
            'del_flg' => 0,
            'update_user_id' => $faker->numberBetween(1, 5),
        ]);
        \App\Model\Config::create([
            'key' => 'reset_interval',
            'value' => 24,
            'memo'  => 'ログイン日時の変更基準間隔を設定。現在時とログイン日時の間隔が●時間以上でログイン日時の更新を判定するために活用
              例）24時間にする場合、"24"を設定',
            'del_flg' => 0,
            'update_user_id' => $faker->numberBetween(1, 5),
        ]);

        // レコード20件分出力
        for($i=0; $i < 20; $i++){
            \App\Model\Config::create([
                'key' => 'key'.$i,
                'value' => $faker->words($nb = 1, $asText = true),
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
