<?php

namespace RiseTechApps\Contact\Traits\HasContacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RiseTechApps\Contact\Events\ContactEvent;
use RiseTechApps\Contact\Models\Contact;
use RiseTechApps\Contact\Support\ContactPayloadResolver;

trait HasContacts
{
    public static function bootHasContacts(): void
    {
        static::saved(function (Model $model) {
            event(new ContactEvent($model));
        });

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            static::restored(function ($model) {
            });
        }
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contact')->ordered();
    }

    /**
     * Sincroniza os contatos deste modelo.
     *
     * Atualiza contatos existentes (por id), cria os novos, remove os ausentes
     * e garante que exista exatamente um contato primário.
     *
     * Aceita um array (lista de contatos, ['contacts' => [...]], 'person.contacts')
     * ou um Illuminate\Http\Request — útil tanto em controllers quanto em jobs.
     *
     * @param Request|array $data
     * @return void
     */
    public function syncContacts(Request|array $data): void
    {
        $contacts = ContactPayloadResolver::resolve($data);

        if (empty($contacts)) {
            return;
        }

        DB::transaction(function () use ($contacts) {
            $existingContacts = $this->contacts()->withTrashed()->get()->keyBy('id');
            $processedIds = [];
            $sortOrder = 0;

            foreach ($contacts as $contactData) {
                $sortOrder++;
                $contactData['sort_order'] = $sortOrder;
                $contactData['contact_type'] = get_class($this);
                $contactData['contact_id'] = $this->getKey();

                $contactId = $contactData['id'] ?? null;

                if ($contactId && isset($existingContacts[$contactId])) {
                    // Atualiza contato existente
                    $existingContacts[$contactId]->update($contactData);
                    $existingContacts[$contactId]->restoreIfTrashed();
                    $processedIds[] = $contactId;
                } else {
                    // Cria novo contato
                    $newContact = Contact::create($contactData);
                    $processedIds[] = $newContact->getKey();
                }
            }

            // Remove contatos que não estão mais presentes
            $idsToDelete = $existingContacts->keys()->diff($processedIds);
            if ($idsToDelete->isNotEmpty()) {
                $this->contacts()->whereIn('id', $idsToDelete)->delete();
            }

            // Garante um único contato primário
            $this->ensureSinglePrimaryContact($processedIds);
        });
    }

    protected function ensureSinglePrimaryContact(array $contactIds): void
    {
        if (empty($contactIds)) {
            return;
        }

        $primaryContact = $this->contacts()
            ->whereIn('id', $contactIds)
            ->where('is_primary', true)
            ->first();

        if ($primaryContact) {
            // Remove o flag primário dos demais
            $this->contacts()
                ->whereIn('id', $contactIds)
                ->where('id', '!=', $primaryContact->getKey())
                ->update(['is_primary' => false]);
        } else {
            // Marca o primeiro como primário se nenhum estiver marcado
            $firstContactId = $contactIds[0] ?? null;
            if ($firstContactId) {
                $this->contacts()
                    ->where('id', $firstContactId)
                    ->update(['is_primary' => true]);
            }
        }
    }

    public function getPrimaryContact(): ?Contact
    {
        return $this->contacts()
            ->primary()
            ->first();
    }

    public function getContactsByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $this->contacts()
            ->where('department', $type)
            ->get();
    }

    public function hasEmail(string $email): bool
    {
        return $this->contacts()
            ->where('email', $email)
            ->exists();
    }

    public function hasContact(string $type, string $value): bool
    {
        return $this->contacts()
            ->where(function ($query) use ($type, $value) {
                $query->where($type, $value);
            })
            ->exists();
    }
}
