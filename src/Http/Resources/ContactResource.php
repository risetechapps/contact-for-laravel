<?php

namespace RiseTechApps\Contact\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'cellphone' => $this->telephone,
            'department' => $this->department,
            'deleted' => !is_null($this->deleted_at),
        ];
    }
}
