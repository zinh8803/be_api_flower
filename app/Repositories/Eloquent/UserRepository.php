<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['role'] = $data['role'] ?? 'user';
        $data['status'] = $data['status'] ?? 1;
        return User::create($data);
    }

    public function createEmployee(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['role'] = $data['role'] ?? 'employee';
        $data['status'] = $data['status'] ?? 1;
        return User::create($data);
    }

    public function updateEmployee(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $data['name'] = $data['name'] ?? $user->name;
        $data['phone'] = $data['phone'] ?? $user->phone;
        $data['address'] = $data['address'] ?? $user->address;
        $data['status'] = $data['status'] ?? $user->status;
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user->fill($data);
        $user->save();
        return $user;
    }
    public function findById(int $id)
    {
        return User::findOrFail($id);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function updateUser(int $id, array $data)
    {
        $user = User::find($id);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile && $data['image']->isValid()) {
            try {
                $imageUrl = ImageHelper::uploadImage($data['image'], 'avatars');
                Log::info('Image uploaded successfully', ['image_url' => $imageUrl]);
                if ($imageUrl) {
                    $data['image_url'] = $imageUrl;
                }
                unset($data['image']);
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
            }
        }

        $user->name = $data['name'] ?? $user->name;
        $user->phone = $data['phone'] ?? $user->phone;
        $user->address = $data['address'] ?? $user->address;
        if (isset($data['image_url'])) {
            $user->image_url = $data['image_url'];
        }
        $user->save();

        return $user;
    }
    public function changePassword(string $oldPassword, string $newPassword)
    {
        $user = Auth::user();

        if (!password_verify($oldPassword, $user->password)) {
            throw new \Exception('Mật khẩu cũ không khớp');
        }

        $user->password = bcrypt($newPassword);
        $user->save();
    }

    public function resetPassword(string $email, string $newPassword)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new \Exception('Người dùng không tồn tại');
        }
        $user->password = bcrypt($newPassword);
        $user->save();
        return $user;
    }
    public function getAll()
    {
        return User::paginate(10);
    }
}
