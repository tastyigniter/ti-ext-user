<?php

namespace Igniter\User\Tests\Models\Concerns;

use Igniter\System\Mail\AnonymousTemplateMailable;
use Igniter\User\Models\Concerns\SendsInvite;
use Igniter\User\Models\User;
use Illuminate\Support\Facades\Mail;
use Mockery;

it('adds send_invite to purgeable attributes on boot', function() {
    expect((new User)->getPurgeableAttributes())->toContain('send_invite');
});

it('restores purged values and sends invite on save when send_invite is true', function() {
    Mail::fake();

    $user = User::factory()->create();
    $user->name = 'Test User';
    $user->send_invite = true;
    $user->save();

    Mail::assertQueued(AnonymousTemplateMailable::class, function($mailable) {
        return $mailable->getTemplateCode() === 'igniter.user::mail.invite';
    });
});

it('does not send invite on save when send_invite is false', function() {
    Mail::fake();

    $user = User::factory()->create();
    $user->name = 'Test User';
    $user->send_invite = false;
    $user->save();

    Mail::assertNotQueued(AnonymousTemplateMailable::class, function($mailable) {
        return $mailable->getTemplateCode() === 'igniter.user::mail.invite';
    });
});

it('throws exception when mailSendsInvite is called without implementing sendsInviteGetTemplateCode', function() {
    $model = Mockery::mock(SendsInvite::class)->makePartial();

    $this->expectException(\LogicException::class);
    $this->expectExceptionMessage('The model ['.get_class($model).'] must implement a sendsInviteGetTemplateCode() method.');

    $model->mailSendInvite();
});

it('updates reset_code, reset_time, and invited_at when SendsInvite is called', function() {
    Mail::fake();

    $user = User::factory()->create();
    $user->name = 'Test User';
    $user->send_invite = true;
    $user->save();

    $user = $user->fresh();

    expect($user->reset_code)->not->toBeNull()
        ->and($user->reset_time)->not->toBeNull()
        ->and($user->invited_at)->not->toBeNull();
});
