<?php

use Illuminate\Database\Seeder;

class CommunityLocationTableSeeder extends Seeder
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

        // レコード30件分出力
        for($i=0; $i < 30; $i++){
            \App\Model\CommunityLocation::create([
                'name' => 'spot'.$i,
                'latitude' => $faker->numberBetween(34, 36),
                'longitude' => $faker->numberBetween(133, 140),
                'memo'   => 'テストメモ'.$i,
                'community_id' => $faker->numberBetween(1, 30),
                'user_id' => $faker->numberBetween(1, 30),
                'marker_id' => $faker->numberBetween(1, 30),
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
    }
}
