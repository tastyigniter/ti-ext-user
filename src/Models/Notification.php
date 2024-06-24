<?php

namespace Igniter\User\Models;

use Igniter\Flame\Database\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use Prunable;

    public function getTitleAttribute()
    {
        return array_get($this->data ?? [], 'title');
    }

    public function getMessageAttribute()
    {
        return array_get($this->data ?? [], 'message');
    }

    public function getUrlAttribute()
    {
        return array_get($this->data ?? [], 'url');
    }

    public function getIconAttribute()
    {
        return array_get($this->data ?? [], 'icon');
    }

    public function getIconColorAttribute()
    {
        return array_get($this->data ?? [], 'iconColor');
    }

    public function scopeWhereNotifiable(Builder $query, Model $notifiable)
    {
        return $query->whereMorphedTo('notifiable', $notifiable);
    }

    //
    // Concerns
    //

    public function prunable(): Builder
    {
        return static::query()
            ->whereNotNull('read_at')
            ->where('read_at', '<=', now()->subDays(setting('activity_log_timeout', 60)));
    }
}
