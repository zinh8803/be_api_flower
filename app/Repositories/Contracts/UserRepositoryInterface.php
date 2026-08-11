<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll();
    public function createEmployee(array $data);
    public function findByEmail(string $email);
    public function updateEmployee(int $id, array $data);
    public function updateUser(int $id, array $data);
    public function changePassword(string $oldPassword, string $newPassword);
    public function resetPassword(string $email, string $newPassword);
    public function getAllEmployees();
    public function getAllUserSubscribed();
    public function updateUserSubscribed(int $id);
    public function handleUpdateStatus(int $id);
}
