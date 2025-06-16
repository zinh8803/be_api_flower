<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {

        $data['password'] = bcrypt($data['password']);
        $data['role'] = $data['role'] ?? 'user'; // Default role is 'user'
        $data['status'] = $data['status'] ?? 1; // Default status is 'active'
        return User::create($data);
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);

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
}

