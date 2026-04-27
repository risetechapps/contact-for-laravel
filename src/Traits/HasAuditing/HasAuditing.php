<?php

namespace RiseTechApps\Contact\Traits\HasAuditing;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use RiseTechApps\Contact\Models\ContactHistory;

trait HasAuditing
{
    public static function bootHasAuditing(): void
    {
        static::created(function ($model) {
            $model->audit('created');
        });

        static::updated(function ($model) {
            $model->audit('updated');
        });

        static::deleted(function ($model) {
            $model->audit('deleted');
        });

        static::restored(function ($model) {
            $model->audit('restored');
        });
    }

    /**
     * Create audit log entry.
     */
    public function audit(string $action): void
    {
        $changes = $this->getAuditChanges($action);

        if ($action === 'updated' && empty($changes['old']) && empty($changes['new'])) {
            return;
        }

        ContactHistory::create([
            'contact_id' => $this->getKey(),
            'action' => $action,
            'changes' => $changes,
            'user_id' => $this->getAuditUserId(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::url(),
            'created_at' => now(),
        ]);
    }

    /**
     * Get changes for audit.
     */
    protected function getAuditChanges(string $action): array
    {
        $auditableFields = $this->getAuditableFields();

        return match ($action) {
            'created' => [
                'old' => [],
                'new' => $this->only($auditableFields),
            ],
            'updated' => [
                'old' => array_intersect_key($this->getOriginal(), array_flip($auditableFields)),
                'new' => $this->only($auditableFields),
            ],
            'deleted', 'restored' => [
                'old' => [],
                'new' => ['deleted_at' => $this->deleted_at],
            ],
            default => ['old' => [], 'new' => []],
        };
    }

    /**
     * Get fields that should be audited.
     */
    protected function getAuditableFields(): array
    {
        if (property_exists($this, 'auditable')) {
            return $this->auditable;
        }

        return $this->getFillable();
    }

    /**
     * Get user ID for audit.
     */
    protected function getAuditUserId(): ?string
    {
        $user = Auth::guard($this->getAuditGuard())->user();

        return $user ? $user->getAuthIdentifier() : null;
    }

    /**
     * Get auth guard for audit.
     */
    protected function getAuditGuard(): ?string
    {
        if (property_exists($this, 'auditGuard')) {
            return $this->auditGuard;
        }

        return config('auth.defaults.guard');
    }

    /**
     * Get audit history.
     */
    public function histories()
    {
        return $this->hasMany(ContactHistory::class, 'contact_id')->orderByDesc('created_at');
    }

    /**
     * Get latest audit history.
     */
    public function latestHistory(int $limit = 10)
    {
        return $this->histories()->limit($limit)->get();
    }

    /**
     * Get history for a specific action.
     */
    public function historiesForAction(string $action)
    {
        return $this->histories()->where('action', $action)->get();
    }

    /**
     * Get last updated by user.
     */
    public function lastUpdatedBy(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        $history = $this->histories()
            ->where('action', 'updated')
            ->first();

        return $history ? $history->user : null;
    }

    /**
     * Get first created by user.
     */
    public function createdBy(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        $history = $this->histories()
            ->where('action', 'created')
            ->first();

        return $history ? $history->user : null;
    }
}
