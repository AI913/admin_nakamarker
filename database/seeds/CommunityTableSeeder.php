<?php

use Illuminate\Database\Seeder;

class CommunityTableSeeder extends Seeder
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
            \App\Model\Community::create([
                'type' => $faker->numberBetween(1, 4),
                'name' => 'community'.$i,
                'status' => 1,
                'del_flg' => 0,
                'host_user_id' => $faker->numberBetween(1, 10),
                'update_user_id' => $faker->numberBetween(1, 5),
            ]);
        }
        \App\Model\Community::create([
            'type' => 3,
            'name' => 'aaa',
            'status' => 1,
            'del_flg' => 0,
            'description' => 'これはみんなに使ってもらいたくて作成しました。使う場面は旅をしていてここがホットなスポットだ！インスタ映えするな！と思ったときに共有するために使っていただきたく思います。また、コミュニティでもどんどん活用してもらっても構いません。素敵なマーカーライフをエンジョイしましょう！',
            'host_user_id' => 1,
            'update_user_id' => $faker->numberBetween(1, 5),
        ]);
    }
}
