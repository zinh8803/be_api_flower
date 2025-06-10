<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User Resource",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Nguyen Van A"),
 *     @OA\Property(property="email", type="string", format="email", example="a@example.com"),
 *     @OA\Property(property="phone", type="string", example="0909123456"),
 *     @OA\Property(property="address", type="string", example="Hậu Giang"),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="avatar_url", type="string", example="http://localhost/storage/avatar.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-09T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-09T10:00:00Z")
 * )
 */

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'role' => $this->role,
            'status' => $this->status,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
