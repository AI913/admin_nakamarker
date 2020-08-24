<?php
namespace App\Services\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
     * 指定メールアドレスユーザーが存在するかどうかチェックする
     * @param $email
     * @param int $user_id
     * @return mixed
     */
    public function isUserForEmail($email, $user_id=0) {
        $conditions["email"] = $email;
        // ユーザーIDが指定されていれば
        if ($user_id) {
            $conditions["users.id@not"] = $user_id;
        }

        return $this->searchExists($conditions);
    }

}