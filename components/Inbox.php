<?php namespace Igniter\User\Components;

use Auth;
use System\Models\Messages_model;

class Inbox extends \System\Classes\BaseComponent
{
    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'label' => 'Page Number',
                'type' => 'string',
            ],
            'itemsPerPage' => [
                'label' => 'Items Per Page',
                'type' => 'number',
                'default' => 20,
            ],
            'sortOrder' => [
                'label' => 'Sort order',
                'type' => 'string',
            ],
        ];
    }

    public function onRun()
    {
        $this->page['customerMessages'] = $this->loadMessages();
    }

    protected function loadMessages()
    {
        if (!$customer = Auth::customer())
            return [];

        return Messages_model::listFrontEnd([
            'page' => $this->param('page'),
            'pageLimit' => $this->property('itemsPerPage'),
            'sort' => $this->property('sortOrder', 'date_added desc'),
            'recipient' => $customer,
        ]);
    }
}