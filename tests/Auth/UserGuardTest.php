<?php

namespace Igniter\User\Tests\Auth;

use Igniter\User\Auth\UserGuard;
use Igniter\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Mockery;

it('checks if user is logged in', function() {
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->shouldReceive('check')->andReturn(true);

    $result = $guard->isLogged();

    expect($result)->toBeTrue();
});

it('checks if user is super user', function() {
    $user = Mockery::mock(User::class)->makePartial();
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $user->shouldReceive('isSuperUser')->andReturn(true);
    $guard->shouldReceive('user')->andReturn($user);

    $result = $guard->isSuperUser();

    expect($result)->toBeTrue();
});

it('returns staff instance', function() {
    $user = Mockery::mock(User::class)->makePartial();
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->shouldReceive('user')->andReturn($user);

    $result = $guard->staff();

    expect($result)->toBe($user);
});

it('returns user locations', function() {
    $locations = Mockery::mock(Collection::class);
    $user = Mockery::mock(User::class)->makePartial();
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->shouldReceive('user')->andReturn($user);
    $user->shouldReceive('getAttribute')->with('locations')->andReturn($locations);

    $result = $guard->locations();

    expect($result)->toBe($locations);
});

it('returns user id', function() {
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->shouldReceive('id')->andReturn(1);

    $result = $guard->getId();

    expect($result)->toBe(1);
});

it('returns user name', function() {
    $user = Mockery::mock(User::class)->makePartial();
    $user->username = 'john_doe';
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->setUser($user);

    $result = $guard->getUserName();

    expect($result)->toBe('john_doe');
});

it('returns user email', function() {
    $user = Mockery::mock(User::class)->makePartial();
    $user->email = 'john.doe@example.com';
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->setUser($user);

    $result = $guard->getUserEmail();

    expect($result)->toBe('john.doe@example.com');
});

it('returns staff name', function() {
    $user = Mockery::mock(User::class)->makePartial();
    $user->name = 'John Doe';
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->setUser($user);

    $result = $guard->getStaffName();

    expect($result)->toBe('John Doe');
});

it('returns staff email', function() {
    $user = Mockery::mock(User::class)->makePartial();
    $user->email = 'john.doe@example.com';
    $guard = Mockery::mock(UserGuard::class)->makePartial();
    $guard->setUser($user);

    $result = $guard->getStaffEmail();

    expect($result)->toBe('john.doe@example.com');
});
