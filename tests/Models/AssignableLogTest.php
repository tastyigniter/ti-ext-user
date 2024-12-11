<?php

namespace Igniter\User\Tests\Models;

use Igniter\Admin\Models\Status;
use Igniter\Cart\Models\Order;
use Igniter\Flame\Database\Model;
use Igniter\Flame\Database\Query\Builder as QueryBuilder;
use Igniter\User\Models\AssignableLog;
use Igniter\User\Models\User;
use Igniter\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\DB;
use Mockery;

it('creates log and updates assignee_updated_at', function() {
    $assignable = Mockery::mock(Order::class)->makePartial();
    $assignable->shouldReceive('getMorphClass')->andReturn('order');
    $assignable->shouldReceive('getKey')->andReturn(1);
    $assignable->assignee_group_id = 1;
    $assignable->assignee_id = 2;
    $assignable->status_id = 3;
    $assignable->shouldReceive('newQuery->where->update')->with(Mockery::on(function($data) {
        return isset($data['assignee_updated_at']);
    }))->once();

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('getKey')->andReturn(1);

    $result = AssignableLog::createLog($assignable, $user);

    expect($result)->toBeInstanceOf(AssignableLog::class)
        ->and($result->user_id)->toBe(1)
        ->and($result->status_id)->toBe(3);
});

it('creates log without user and updates assignee_updated_at', function() {
    $assignable = Mockery::mock(Order::class)->makePartial();
    $assignable->shouldReceive('getMorphClass')->andReturn('order');
    $assignable->shouldReceive('getKey')->andReturn(1);
    $assignable->assignee_group_id = 1;
    $assignable->assignee_id = 2;
    $assignable->status_id = 3;
    $assignable->shouldReceive('newQuery->where->update')->with(Mockery::on(function($data) {
        return isset($data['assignee_updated_at']);
    }))->once();

    $result = AssignableLog::createLog($assignable);

    expect($result)->toBeInstanceOf(AssignableLog::class)
        ->and($result->user_id)->toBeNull()
        ->and($result->status_id)->toBe(3);
});

it('returns true when assignable type is order', function() {
    $log = Mockery::mock(AssignableLog::class)->makePartial();
    $log->assignable_type = \Igniter\Cart\Models\Order::make()->getMorphClass();

    $result = $log->isForOrder();

    expect($result)->toBeTrue();
});

it('returns false when assignable type is not order', function() {
    $log = Mockery::mock(AssignableLog::class)->makePartial();
    $log->assignable_type = 'some_other_type';

    $result = $log->isForOrder();

    expect($result)->toBeFalse();
});

it('applies assignable scope with correct type and id', function() {
    $query = Mockery::mock(QueryBuilder::class);
    $assignable = Mockery::mock(Model::class);
    $assignable->shouldReceive('getMorphClass')->andReturn('order');
    $assignable->shouldReceive('getKey')->andReturn(1);

    $query->shouldReceive('where')->with('assignable_type', 'order')->andReturnSelf();
    $query->shouldReceive('where')->with('assignable_id', 1)->andReturnSelf();

    $log = new AssignableLog;
    $result = $log->scopeApplyAssignable($query, $assignable);

    expect($result)->toBe($query);
});

it('applies round robin scope with correct order', function() {
    $query = Mockery::mock(QueryBuilder::class);
    $query->shouldReceive('select')->with('assignee_id')->andReturnSelf();
    $query->shouldReceive('selectRaw')->with('MAX(created_at) as assign_value')->andReturnSelf()->once();
    $query->shouldReceive('whereIn')->with('status_id', Mockery::type('array'))->andReturnSelf();
    $query->shouldReceive('whereNotNull')->with('assignee_id')->andReturnSelf();
    $query->shouldReceive('groupBy')->with('assignee_id')->andReturnSelf();
    $query->shouldReceive('orderBy')->with('assign_value', 'asc')->andReturnSelf();

    $log = new AssignableLog;
    $result = $log->scopeApplyRoundRobinScope($query);

    expect($result)->toBe($query);
});

it('applies load balanced scope with correct limit', function() {
    $query = Mockery::mock(QueryBuilder::class);
    $limit = 10;
    $query->shouldReceive('select')->with('assignee_id')->andReturnSelf();
    $query->shouldReceive('selectRaw')->with('COUNT(assignee_id)/'.DB::getPdo()->quote($limit).' as assign_value')->andReturnSelf()->once();
    $query->shouldReceive('whereIn')->with('status_id', Mockery::type('array'))->andReturnSelf();
    $query->shouldReceive('whereNotNull')->with('assignee_id')->andReturnSelf();
    $query->shouldReceive('groupBy')->with('assignee_id')->andReturnSelf();
    $query->shouldReceive('orderBy')->with('assign_value', 'desc')->andReturnSelf();
    $query->shouldReceive('havingRaw')->with('assign_value < 1')->andReturnSelf()->once();

    $log = new AssignableLog;
    $result = $log->scopeApplyLoadBalancedScope($query, $limit);

    expect($result)->toBe($query);
});

it('applies scope with correct assignee id', function() {
    $query = Mockery::mock(QueryBuilder::class);
    $assigneeId = 1;
    $query->shouldReceive('where')->with('assignee_id', $assigneeId)->andReturnSelf()->once();

    $log = new AssignableLog;
    $result = $log->scopeWhereAssignTo($query, $assigneeId);

    expect($result)->toBe($query);
});

it('applies scope with correct assignee group id', function() {
    $query = Mockery::mock(QueryBuilder::class);
    $assigneeGroupId = 1;
    $query->shouldReceive('where')->with('assignee_group_id', $assigneeGroupId)->andReturnSelf()->once();

    $log = new AssignableLog;
    $result = $log->scopeWhereAssignToGroup($query, $assigneeGroupId);

    expect($result)->toBe($query);
});

it('applies scope with correct assignee group ids', function() {
    $query = Mockery::mock(QueryBuilder::class);
    $assigneeGroupIds = [1, 2, 3];
    $query->shouldReceive('whereIn')->with('assignee_group_id', $assigneeGroupIds)->andReturnSelf()->once();

    $log = new AssignableLog;
    $result = $log->scopeWhereInAssignToGroup($query, $assigneeGroupIds);

    expect($result)->toBe($query);
});

it('returns prunable records older than activity log timeout', function() {
    $this->travelTo($date = now()->startOfDay());

    $result = (new AssignableLog)->prunable()->toRawSql();
    expect($result)->toContain("`created_at` <= '".$date->subDays(60)->toDateTimeString()."'");
});

it('configures assignable log model correctly', function() {
    $log = new AssignableLog;

    expect(class_uses_recursive($log))
        ->toContain(Prunable::class)
        ->and($log->getTable())->toBe('assignable_logs')
        ->and($log->getKeyName())->toBe('id')
        ->and($log->getGuarded())->toBe([])
        ->and($log->timestamps)->toBeTrue()
        ->and($log->getCasts()['assignable_id'])->toBe('integer')
        ->and($log->getCasts()['assignee_group_id'])->toBe('integer')
        ->and($log->getCasts()['assignee_id'])->toBe('integer')
        ->and($log->getCasts()['user_id'])->toBe('integer')
        ->and($log->getCasts()['status_id'])->toBe('integer')
        ->and($log->getMorphClass())->toBe('assignable_logs')
        ->and($log->relation['belongsTo']['user'])->toBe(User::class)
        ->and($log->relation['belongsTo']['assignee'])->toBe(User::class)
        ->and($log->relation['belongsTo']['assignee_group'])->toBe(UserGroup::class)
        ->and($log->relation['belongsTo']['status'])->toBe(Status::class)
        ->and($log->relation['morphTo']['assignable'])->toBe([]);
});
