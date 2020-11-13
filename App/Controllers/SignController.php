<?php
namespace App\Controllers;

use Auth\Auth;
use Core\Base\Controller;
use App\Repositories\UsersRepository;

class SignController extends Controller
{
    public UsersRepository $usersRepo;

    public function __construct()
    {
        parent::__construct();
        $this->usersRepo = new UsersRepository();
    }

    public function signIn($data)
    {
        $uid = $data['userId'];
        $pwd = hash('sha512', $data['passWd']);

        // 讀 DB
        $user = $this->usersRepo->signIn($uid, $pwd);

        if ($pwd == $user[0]['password']) {
            // 登入成功
            Auth::userSignInUp($user);
        } else {
            // 失敗設false
            self::getResponse()->setAccess(false);
            $this->signOut();
        }
    }

    public function signUp($data)
    {
        $uid = $data['userId'];
        $pwd = hash('sha512', $data['passWd']);
        $eml = $data['email'];

        // 寫 DB
        $this->usersRepo->signUp($uid, $pwd, $eml);

        // 取得登入權限
        $this->signIn($data);
    }

    public function signOut()
    {
        Auth::userSignOut();
    }
}
