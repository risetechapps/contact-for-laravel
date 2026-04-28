<?php

namespace RiseTechApps\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use RiseTechApps\Contact\Traits\HasAuditing\HasAuditing;
use RiseTechApps\HasUuid\Traits\HasUuid;
use RiseTechApps\Monitoring\Traits\HasLoggly\HasLoggly;
use RiseTechApps\ToUpper\Traits\HasToUpper;
use Illuminate\Database\Eloquent\Builder;

class Contact extends Model
{
    use HasFactory, Notifiable, HasUuid, SoftDeletes, HasToUpper, HasLoggly;
    use Prunable, HasAuditing;

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($contact) {
            if ($contact->isDirty('is_primary') && $contact->is_primary) {
                $contact->clearOtherPrimaryContacts();
            }
        });

        static::created(function ($contact) {
            $contact->ensurePrimaryContact();
        });

        static::deleting(function ($contact) {
            if ($contact->is_primary) {
                $contact->promoteAnotherContact();
            }
        });
    }

    /**
     * Clear primary flag from other contacts of the same parent.
     */
    protected function clearOtherPrimaryContacts(): void
    {
        static::where('contact_type', $this->contact_type)
            ->where('contact_id', $this->contact_id)
            ->where('id', '!=', $this->getKey())
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }

    /**
     * Promote another contact to primary when this one is being deleted.
     */
    protected function promoteAnotherContact(): void
    {
        $nextContact = static::where('contact_type', $this->contact_type)
            ->where('contact_id', $this->contact_id)
            ->where('id', '!=', $this->getKey())
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->first();

        if ($nextContact) {
            $nextContact->update(['is_primary' => true]);
        }
    }

    /**
     * Ensure this contact is marked as primary if no primary exists for the parent model.
     */
    protected function ensurePrimaryContact(): void
    {
        if ($this->is_primary) {
            return;
        }

        $hasPrimary = static::where('contact_type', $this->contact_type)
            ->where('contact_id', $this->contact_id)
            ->where('is_primary', true)
            ->exists();

        if (! $hasPrimary) {
            $this->update(['is_primary' => true]);
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'telephone',
        'cellphone',
        'email',
        'department',
        'contact_type',
        'contact_id',
        'is_primary',
        'sort_order',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'contact_type',
        'contact_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function prunable(): Contact|Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(1));
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function restoreIfTrashed(): void
    {
        if ($this->trashed()) {
            $this->restore();
        }
    }
}
