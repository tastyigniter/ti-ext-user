<?php

namespace Igniter\User\EventRules\Actions;

use Igniter\EventRules\Classes\BaseAction;

class SendMailTemplate extends BaseAction
{
    public function actionDetails()
    {
        return [
            'name' => 'Compose a mail template',
            'description' => 'Send a message to a recipient',
        ];
    }
}