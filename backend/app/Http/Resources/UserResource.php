<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'bio' => $this->bio,
            'location' => $this->location,
            'avatar' => $this->avatar,
            'last_login' => $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : null,
            'last_activity' => $this->last_activity ? $this->last_activity->format('Y-m-d H:i:s') : null,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name')->toArray();
            }),
            'permissions' => $this->when($this->relationLoaded('roles'), function () {
                // Get all permissions (direct + from roles)
                $allPermissions = $this->getAllPermissions();
                return $allPermissions->pluck('name')->toArray();
            }),
        ];
    }
}