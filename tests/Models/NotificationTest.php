<?php

namespace Igniter\User\Tests\Models;

use Igniter\User\Models\Notification;
use Igniter\User\Models\User;
use Illuminate\Database\Eloquent\Prunable;

it('can get title attribute', function() {
    $notification = new Notification;
    $notification->data = ['title' => 'Test Title'];

    expect($notification->title)->toEqual('Test Title');
});

it('can get message attribute', function() {
    $notification = new Notification;
    $notification->data = ['message' => 'Test Message'];

    expect($notification->message)->toEqual('Test Message');
});

it('can get url attribute', function() {
    $notification = new Notification;
    $notification->data = ['url' => 'http://localhost'];

    expect($notification->url)->toEqual('http://localhost');
});

it('can get icon attribute', function() {
    $notification = new Notification;
    $notification->data = ['icon' => 'fa fa-bell'];

    expect($notification->icon)->toEqual('fa fa-bell');
});

it('can get icon color attribute', function() {
    $notification = new Notification;
    $notification->data = ['iconColor' => '#ff0000'];

    expect($notification->iconColor)->toEqual('#ff0000');
});

it('can scope where notifiable', function() {
    $query = Notification::query();
    $notifiable = User::factory()->create();

    $query = $query->whereNotifiable($notifiable);

    expect($query->toSql())->toContain('where (`notifications`.`notifiable_type` = ? and `notifications`.`notifiable_id` = ?)');
});

it('can prune notifications', function() {
    $query = (new Notification)->prunable();

    expect($query->toSql())->toContain('`read_at` is not null and `read_at` <= ?');
});

it('configures automation logs correctly', function() {
    $model = new Notification;

    expect(class_uses_recursive($model))
        ->toHaveKey(Prunable::class);
});
