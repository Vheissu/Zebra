<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->username,
            'karma' => $this->karma,
            'about' => $this->about,
            'is_admin' => $this->is_admin,
            'created_at' => $this->created_at,
            'url' => route('users.show', $this->resource),
            'email' => $this->when($request->user()?->is($this->resource), $this->email),
        ];
    }
}
