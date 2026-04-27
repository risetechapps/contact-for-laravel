<?php

namespace RiseTechApps\Contact\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'contact_id' => $this->contact_id,
            'action' => $this->action,
            'action_label' => $this->getActionLabel(),
            'changes' => $this->changes,
            'changed_fields' => $this->getChangedFields(),
            'user' => $this->when($this->user, [
                'id' => $this->user->getKey(),
                'name' => $this->user->name ?? $this->user->email,
            ]),
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'url' => $this->url,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Get human-readable action label.
     */
    private function getActionLabel(): string
    {
        return match ($this->action) {
            'created' => 'Criado',
            'updated' => 'Atualizado',
            'deleted' => 'Excluído',
            'restored' => 'Restaurado',
            default => $this->action,
        };
    }
}
