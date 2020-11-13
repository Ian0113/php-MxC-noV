<?php
namespace App\Repositories;

use Core\Base\Repository;
use App\Models\UsersModel;

class UsersRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new UsersModel;
    }

    public function signUp(string $userId, string $passWd, string $email)
    {
        $this->model->insert([
            'user_id' => $userId,
            'password' => $passWd,
            'email' => $email
        ]);
    }

    public function signIn(string $userId)
    {
        return $this->model->selectAll([
            ['user_id', '=', $userId]
        ]);
    }
}