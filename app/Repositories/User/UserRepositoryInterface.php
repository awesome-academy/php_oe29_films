<?php
namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function getUsers($request);

    public function changeStatus($userId);

    public function changePassword($userId, $password);

    public function findUserByEmail($request);

    public function getNewUserInMonth();

    public function storeQueue($user, $password);
}
