<?php

namespace Igniter\User\Tests\Models\Concerns;

use Igniter\Flame\Database\Builder;
use Igniter\Flame\Database\Model;
use Igniter\User\Models\Concerns\HasCustomer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Mockery;

beforeEach(function() {
    $this->model = Mockery::mock(HasCustomer::class)->makePartial()->shouldAllowMockingProtectedMethods();
});

it('applies customer scope with customer id as integer', function() {
    $query = Mockery::mock(Builder::class);
    $relation = Mockery::mock(Relation::class);
    $relation->shouldReceive('getQualifiedForeignKeyName')->andReturn('customer_id');
    $this->model->shouldReceive('customer')->andReturn($relation);
    $this->model->shouldReceive('getRelationType')->andReturn('belongsTo');
    $query->shouldReceive('where')->with('customer_id', 1)->andReturnSelf();

    $result = $this->model->scopeApplyCustomer($query, 1);

    expect($result)->toBe($query);
});

it('applies customer scope with customer id as model', function() {
    $query = Mockery::mock(Builder::class);
    $customer = Mockery::mock(Model::class)->makePartial();
    $relation = Mockery::mock(Relation::class);
    $customer->shouldReceive('getKey')->andReturn(1);
    $relation->shouldReceive('getQualifiedForeignKeyName')->andReturn('customer_id');
    $this->model->shouldReceive('customer')->andReturn($relation);
    $this->model->shouldReceive('getRelationType')->andReturn('belongsTo');
    $query->shouldReceive('where')->with('customer_id', 1)->andReturnSelf();

    $result = $this->model->scopeApplyCustomer($query, $customer);

    expect($result)->toBe($query);
});

it('applies customer scope with hasMany relation', function() {
    $query = Mockery::mock(Builder::class);
    $relation = Mockery::mock(Relation::class);
    $relation->shouldReceive('getQualifiedForeignKeyName')->andReturn('customer_id');
    $this->model->shouldReceive('customer')->andReturn($relation);
    $this->model->shouldReceive('getRelationType')->andReturn('hasMany');
    $query->shouldReceive('whereHas')->with('customer', Mockery::on(function($callback) {
        $subQuery = Mockery::mock(Builder::class);
        $subQuery->shouldReceive('where')->with('customer_id', 1)->andReturnSelf();
        $callback($subQuery);

        return true;
    }))->andReturnSelf();

    $result = $this->model->scopeApplyCustomer($query, 1);

    expect($result)->toBe($query);
});

it('returns customer relation name from constant', function() {
    $model = new class extends Model
    {
        public const CUSTOMER_RELATION = 'custom_relation';

        use HasCustomer;

        public function getNameInTest()
        {
            return $this->getCustomerRelationName();
        }
    };

    $result = $model->getNameInTest();

    expect($result)->toBe('custom_relation');
});

it('returns default customer relation name', function() {
    $reflection = new \ReflectionClass($this->model);
    $method = $reflection->getMethod('getCustomerRelationName');
    $method->setAccessible(true);
    $result = $method->invoke($this->model);

    expect($result)->toBe('customer');
});

it('returns true for single relation type', function() {
    $this->model->shouldReceive('getRelationType')->with('customer')->andReturn('hasOne');

    $reflection = new \ReflectionClass($this->model);
    $method = $reflection->getMethod('customerIsSingleRelationType');
    $method->setAccessible(true);
    $result = $method->invoke($this->model);

    expect($result)->toBeTrue();
});

it('returns false for multiple relation type', function() {
    $this->model->shouldReceive('getRelationType')->with('customer')->andReturn('hasMany');

    $reflection = new \ReflectionClass($this->model);
    $method = $reflection->getMethod('customerIsSingleRelationType');
    $method->setAccessible(true);
    $result = $method->invoke($this->model);

    expect($result)->toBeFalse();
});
