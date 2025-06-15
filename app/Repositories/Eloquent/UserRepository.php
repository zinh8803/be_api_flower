<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data) {

        $data['password'] = bcrypt($data['password']);
        $data['role'] = $data['role'] ?? 'user'; // Default role is 'user'
        $data['status'] = $data['status'] ?? 1; // Default status is 'active'
        return User::create($data);
    }

    public function update(int $id, array $data) {
        $user = User::findOrFail($id);
        
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->fill($data);
        $user->save();

        return $user;
    }
    public function findById(int $id) {
        return User::findOrFail($id);
    }

    public function findByEmail(string $email) {
        return User::where('email', $email)->first();
    }
}
