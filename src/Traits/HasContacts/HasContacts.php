<?php

namespace RiseTechApps\Contact\Traits\HasContacts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use RiseTechApps\Contact\Events\ContactEvent;
use RiseTechApps\Contact\Models\Contact;

trait HasContacts
{
    public static function bootHasContacts(): void
    {
        static::saved(function (Model $model) {
            event(new ContactEvent($model));
        });

        static::restored(function ($model) {
        });
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contact')->ordered();
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
