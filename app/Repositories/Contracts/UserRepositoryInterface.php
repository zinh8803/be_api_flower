<?php
namespace App\Repositories\Contracts;

interface UserRepositoryInterface {
    public function create(array $data);
    public function findByEmail(string $email);
}
