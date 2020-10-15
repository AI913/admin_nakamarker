<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Model\PushHistoryService;

class FirebaseNotificationCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:notification_order_check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Firebase Notification Order Check: 1 minutes order check ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PushHistoryService $mainService)
    {
        \Log::info($this->signature);
        
        try {
            \DB::beginTransaction();
            // 検索条件の設定
            $conditions = [];

            // statusが送信前のデータを取得
            $conditions['status'] = config('const.push_before');
            // 送信予約日時が現時刻と一致するデータを取得
            $conditions['reservation_date'] = date("Y-m-d H:i");

            // 現在日時に一致するプッシュ通知データを取得
            $data = $mainService->searchList($conditions);

            // 送信予約日時と同時刻の場合は送信処理を実施
            foreach($data as $value) {
                // 送信処理を記述予定


                // 送信ステータスを送信結果に基づいて変更
                $value->status = config('const.push_after');
                $value->save();
            }
            
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('firebase notification error:'.$e->getMessage());
        }
    }
}
