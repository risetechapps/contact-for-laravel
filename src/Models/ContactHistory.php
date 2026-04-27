<?php

namespace RiseTechApps\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RiseTechApps\HasUuid\Traits\HasUuid;

class ContactHistory extends Model
{
    use HasFactory, HasUuid;

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_id',
        'action',
        'changes',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the contact that owns the history.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Get the user who made the change.
     */
    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model', \App\Models\User::class), 'user_id');
    }

    /**
     * Get old values from changes.
     */
    public function getOldValues(): array
    {
        return $this->changes['old'] ?? [];
    }

    /**
     * Get new values from changes.
     */
    public function getNewValues(): array
    {
        return $this->changes['new'] ?? [];
    }

    /**
     * Get changed fields list.
     */
    public function getChangedFields(): array
    {
        $old = $this->getOldValues();
        $new = $this->getNewValues();

        return array_keys(array_diff_assoc($new, $old));
    }

    /**
     * Check if a specific field was changed.
     */
    public function fieldChanged(string $field): bool
    {
        return in_array($field, $this->getChangedFields());
    }

    /**
     * Get the old value of a specific field.
     */
    public function getOldValue(string $field): mixed
    {
        return $this->getOldValues()[$field] ?? null;
    }

    /**
     * Get the new value of a specific field.
     */
    public function getNewValue(string $field): mixed
    {
        return $this->getNewValues()[$field] ?? null;
    }
}
