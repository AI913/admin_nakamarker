<?php

use Illuminate\Database\Seeder;

class UserLocationTableSeeder extends Seeder
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
            \App\Model\UserLocation::create([
                'name' => 'user_spot'.$i,
                'latitude' => $faker->numberBetween(20, 45),
                'longitude' => $faker->numberBetween(122, 153),
                'marker_id' => $faker->numberBetween(1, 15),
                'user_id' => $faker->numberBetween(1, 15),
                'del_flg' => 0,
                'update_user_id' => $faker->numberBetween(1, 15),
            ]);
        }
    }
}
