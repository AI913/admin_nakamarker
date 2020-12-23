<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seedファイル実行
        $this->call(UserTableSeeder::class);
        $this->call(MarkerTableSeeder::class);
        $this->call(CommunityTableSeeder::class);
        $this->call(ConfigTableSeeder::class);
        $this->call(NewsTableSeeder::class);
        $this->call(PushHistoryTableSeeder::class);
        $this->call(UserLocationTableSeeder::class);
        $this->call(UserMarkerTableSeeder::class);
        $this->call(UserPointsHistoryTableSeeder::class);
        $this->call(CommunityHistoryTableSeeder::class);
        $this->call(CommunityLocationTableSeeder::class);
        $this->call(CommunityMarkerTableSeeder::class);
    }
}
