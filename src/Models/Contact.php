<?php

namespace RiseTechApps\Contact\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use RiseTechApps\HasUuid\Traits\HasUuid\HasUuid;
use RiseTechApps\Monitoring\Traits\HasLoggly\HasLoggly;
use RiseTechApps\ToUpper\Traits\HasToUpper;

class Contact extends Model
{
    use HasFactory, Notifiable, HasUuid, SoftDeletes, HasToUpper, HasLoggly;
    use Prunable;

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
        'contact_id'
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
    ];

    public function prunable(): Contact|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subDays(1));
    }
}
