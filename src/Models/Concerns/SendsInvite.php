<?php

namespace Igniter\User\Models\Concerns;

use Igniter\Flame\Database\Model;

trait SendsInvite
{
    public static function bootSendsInvite()
    {
        static::extend(function(Model $model) {
            $model->addPurgeable(['send_invite']);
        });

        static::saved(function(Model $model) {
            $model->restorePurgedValues();
            if ($model->send_invite) {
                $model->sendInvite();
            }
        });
    }

    protected function sendInviteGetTemplateCode(): string
    {
        throw new \LogicException(sprintf(
            'The model [%s] must implement a sendInviteGetTemplateCode() method.',
            get_class($this)
        ));
    }

    public function sendInvite()
    {
        $templateCode = $this->sendInviteGetTemplateCode();

        $this->newQuery()->where($this->getKeyName(), $this->getKey())->update([
            'reset_code' => $inviteCode = $this->generateResetCode(),
            'reset_time' => now(),
            'invited_at' => now(),
        ]);

        $this->bindEventOnce('model.mailGetData', function($view, $recipientType) use ($templateCode, $inviteCode) {
            if ($view === $templateCode) {
                return ['invite_code' => $inviteCode];
            }
        });

        $this->mailSend($templateCode);
    }
}