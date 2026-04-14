<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
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
            'email' => $this->email, // Optional: might want to hide email in public APIs
            'role' => $this->role,
            'major' => $this->major,
            'angkatan' => $this->graduation_year,
            'current_job' => $this->current_job,
            'profile_picture' => $this->hasProfilePicture() ? url($this->getProfilePicture()) : null,
            'slug' => $this->slug,
            'joined_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
