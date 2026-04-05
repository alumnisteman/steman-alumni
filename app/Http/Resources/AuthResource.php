<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->resource['token'] ?? null;
        $user = $this->resource['user'] ?? null;

        return [
            'status' => 'success',
            'message' => 'Authentication successful.',
            'data' => [
                'user' => $user ? new AlumniResource($user) : null,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ];
    }
}
