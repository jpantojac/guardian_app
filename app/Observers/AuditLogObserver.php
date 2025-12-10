<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'create', null, $model->toArray());
    }

    public function updated(Model $model): void
    {
        $old = $model->getOriginal();
        $new = $model->getChanges();

        // Filter out timestamps if desired, but keeping them is fine.
        // We only want to log if there are actual changes.
        if (!empty($new)) {
            $this->log($model, 'update', $old, $new);
        }
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'delete', $model->toArray(), null);
    }

    protected function log(Model $model, string $action, ?array $old, ?array $new): void
    {
        // Avoid infinite loop if we were observing AuditLog itself (which we shouldn't be)
        if ($model instanceof AuditLog) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(), // Null if guest
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(), // Anonymize if needed here or in model mutator
            'user_agent' => request()->userAgent(),
        ]);
    }
}
