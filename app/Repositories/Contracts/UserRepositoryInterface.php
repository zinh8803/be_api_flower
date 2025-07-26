<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function getAll();
    public function createEmployee(array $data);
    public function create(array $data);
    public function findByEmail(string $email);
    public function findById(int $id);
    public function updateEmployee(int $id, array $data);
    public function updateUser(int $id, array $data);
    public function changePassword(string $oldPassword, string $newPassword);
    public function resetPassword(string $email, string $newPassword);
    public function getAllEmployees();
    public function getAllUserSubscribed();
}
