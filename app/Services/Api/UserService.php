<?php
namespace App\Services\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Model\User;

class UserService extends BaseService
{
    /**
     * コンストラクタ
     * CommunityService constructor.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * ユーザートークン発行　md5(ランダム＋id)
     * @param $id
     * @return string
     */
    public function issueUserToken($id) {
        return md5(Str::random(64).$id);
    }

    
}