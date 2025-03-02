<?php

namespace Igniter\User\Models;

use Carbon\Carbon;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Database\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\DB;

/**
 * AssignableLog Model Class
 */
class AssignableLog extends Model
{
    use Prunable;

    /**
     * @var string The database table name
     */
    protected $table = 'assignable_logs';

    /**
     * @var string The database table primary key
     */
    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = true;

    public $relation = [
        'belongsTo' => [
            'assignee' => \Igniter\User\Models\User::class,
            'assignee_group' => \Igniter\User\Models\UserGroup::class,
            'status' => \Igniter\Admin\Models\Status::class,
        ],
        'morphTo' => [
            'assignable' => [],
        ],
    ];

    protected $casts = [
        'assignable_id' => 'integer',
        'assignee_group_id' => 'integer',
        'assignee_id' => 'integer',
        'status_id' => 'integer',
    ];

    /**
     * @param \Igniter\Flame\Database\Model|mixed $assignable
     * @return static|bool
     * @throws \Exception
     */
    public static function createLog($assignable)
    {
        $attributes = [
            'assignable_type' => $assignable->getMorphClass(),
            'assignable_id' => $assignable->getKey(),
            'assignee_group_id' => $assignable->assignee_group_id,
            'assignee_id' => null,
        ];

        self::query()->where($attributes)->delete();

        $model = self::query()->firstOrNew(array_merge($attributes, [
            'assignee_id' => $assignable->assignee_id,
        ]));

        $model->status_id = $assignable->status_id;

        $assignable->newQuery()->where($assignable->getKeyName(), $assignable->getKey())->update([
            'assignee_updated_at' => Carbon::now(),
        ]);

        $model->save();

        return $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUnAssignedQueue($limit)
    {
        return self::query()
            ->whereUnAssigned()
            ->whereHasAutoAssignGroup()
            ->orderBy('created_at', 'desc')
            ->limit($limit);
    }

    public function isForOrder()
    {
        return $this->assignable_type === Order::make()->getMorphClass();
    }

    //
    //
    //

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @param \Igniter\Flame\Database\Model $assignable
     * @return mixed
     */
    public function scopeApplyAssignable($query, $assignable)
    {
        return $query
            ->where('assignable_type', $assignable->getMorphClass())
            ->where('assignable_id', $assignable->getKey());
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeApplyRoundRobinScope($query)
    {
        return $query
            ->select('assignee_id')
            ->selectRaw('MAX(created_at) as assign_value')
            ->whereIn('status_id', setting('processing_order_status', []))
            ->whereNotNull('assignee_id')
            ->groupBy('assignee_id')
            ->orderBy('assign_value', 'asc');
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeApplyLoadBalancedScope($query, $limit)
    {
        return $query
            ->select('assignee_id')
            ->selectRaw('COUNT(assignee_id)/'.DB::getPdo()->quote($limit).' as assign_value')
            ->whereIn('status_id', setting('processing_order_status', []))
            ->whereNotNull('assignee_id')
            ->groupBy('assignee_id')
            ->orderBy('assign_value', 'desc')
            ->havingRaw('assign_value < 1');
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeWhereUnAssigned($query)
    {
        return $query->whereNotNull('assignee_group_id')->whereNull('assignee_id');
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeWhereAssignTo($query, $assigneeId)
    {
        return $query->where('assignee_id', $assigneeId);
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeWhereAssignToGroup($query, $assigneeGroupId)
    {
        return $query->where('assignee_group_id', $assigneeGroupId);
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeWhereInAssignToGroup($query, array $assigneeGroupIds)
    {
        return $query->whereIn('assignee_group_id', $assigneeGroupIds);
    }

    /**
     * @param \Igniter\Flame\Database\Query\Builder $query
     * @return mixed
     */
    public function scopeWhereHasAutoAssignGroup($query)
    {
        return $query->whereHas('assignee_group', function (Builder $query) {
            $query->where('auto_assign', 1);
        });
    }

    //
    // Concerns
    //

    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<=', now()->subDays(setting('activity_log_timeout', 60)));
    }
}
