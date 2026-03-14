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
        return $this->morphMany(Contact::class, 'contacts');
    }
}
