<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService extends BaseService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }

    public function getAllEmployees()
    {
        return $this->userRepository->getAllEmployees();
    }

    public function getUserById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data)
    {
        $this->logInfo('Creating new user', ['email' => $data['email'] ?? null]);
        return $this->userRepository->create($data);
    }

    public function createEmployee(array $data)
    {
        $this->logInfo('Creating new employee', ['email' => $data['email'] ?? null]);
        return $this->userRepository->createEmployee($data);
    }

    public function updateEmployee(int $id, array $data)
    {
        return $this->userRepository->updateEmployee($id, $data);
    }

    public function updateUser(int $id, array $data)
    {
        return $this->userRepository->updateUser($id, $data);
    }

    public function changePassword(string $oldPassword, string $newPassword)
    {
        return $this->userRepository->changePassword($oldPassword, $newPassword);
    }

    public function resetPassword(string $email, string $newPassword)
    {
        return $this->userRepository->resetPassword($email, $newPassword);
    }

    public function getAllUserSubscribed()
    {
        return $this->userRepository->getAllUserSubscribed();
    }

    public function updateUserSubscribed(int $id)
    {
        return $this->userRepository->updateUserSubscribed($id);
    }

    public function handleUpdateStatus(int $id)
    {
        return $this->userRepository->handleUpdateStatus($id);
    }
}
