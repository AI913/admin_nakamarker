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

        // ステータスの値を配列にセット
        $status = [1, 2, 3, 9];

        // レコード30件分出力
        for($i=0; $i < 30; $i++){
            \App\Model\PushHistory::create([
                'title' => 'title'.$i,
                'content' => $faker->sentences($nb = 1, $asText = true),
                'type' => 1,
                'reservation_date' => date_format($faker->dateTimeBetween($startDate = '-10 days', $endDate = '10 days'), 'Y-m-d H:i'), // 本日から前後10日で設定
                'status' => $status[array_rand($status , 1 )],  // ステータスの値をランダムに抽出
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
