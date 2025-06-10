<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data) {

        $data['password'] = bcrypt($data['password']);
        $date['role'] = $data['role'] ?? 'user'; // Default role is 'user'
        $data['status'] = $data['status'] ?? 1; // Default status is 'active'
        return User::create($data);
    }

    public function findByEmail(string $email) {
        return User::where('email', $email)->first();
    }
}
