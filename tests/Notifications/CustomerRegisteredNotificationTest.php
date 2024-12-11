<?php

namespace Igniter\User\Tests\Notifications;

use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Igniter\User\Notifications\CustomerRegisteredNotification;
use Mockery;

beforeEach(function() {
    $this->subject = Mockery::mock(Customer::class)->makePartial();
    $this->notification = CustomerRegisteredNotification::make()->subject($this->subject);
});

it('returns recipients who are enabled super users', function() {
    $user = User::factory()->superUser()->create();

    $result = $this->notification->getRecipients();

    expect($result)->toHaveCount(1)
        ->and($result[0]->getKey())->toBe($user->getKey());
});

it('returns title for customer registered notification', function() {
    expect($this->notification->getTitle())->toBe(lang('igniter.user::default.login.notify_registered_account_title'));
});

it('returns URL for customer edit page', function() {
    $this->subject->shouldReceive('getKey')->andReturn(1);

    $result = $this->notification->getUrl();

    expect($result)->toBe(admin_url('customers/edit/1'));
});

it('returns URL for customers page when subject is null', function() {
    $result = $this->notification->getUrl();

    expect($result)->toBe(admin_url('customers/edit'));
});

it('returns message for customer registered notification', function() {
    $this->subject->shouldReceive('extendableGet')->with('full_name')->andReturn('John Doe');

    $result = $this->notification->getMessage();

    expect($result)->toBe(sprintf(lang('igniter.user::default.login.notify_registered_account'), 'John Doe'));
});

it('returns icon for customer registered notification', function() {
    $result = $this->notification->getIcon();

    expect($result)->toBe('fa-user');
});

it('returns alias for customer registered notification', function() {
    $result = $this->notification->getAlias();

    expect($result)->toBe('customer-registered');
});
