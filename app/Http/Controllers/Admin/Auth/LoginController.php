<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }
    
    /**
     * ログイン処理（削除フラグを見る）
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attemptLogin(Request $request) {
        
        // ステータス「管理者(システムアドミン)」
        if ($this->customAttempt($request)) {
            return true;
        }
        return false;
    }

    /**
     * ログアウト処理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request) {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    /**
     * カスタム認証
     * @param Request $request
     * @return bool
     * attempt()メソッドにより、キーに指定した値を自動的にDBから取得する
     * input()メソッドにより入力値のJSON情報にアクセス
     */
    private function customAttempt(Request $request) {
        if ($this->guard()->attempt(
            [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'del_flg' => 0
            ], $request->filled('remember'))
        ) {
            if ($this->guard()->user()->isAdmin()) {
                // ログイン日時セット
                $this->guard()->user()->login_time = Carbon::now();
                $this->guard()->user()->save();
                return true;
            } else {
                // 権限無しはログアウト処理
                Session::put('admins', 'ログインの権限がありません');
                $this->guard()->logout();
            }
        }
        return false;
    }
}
