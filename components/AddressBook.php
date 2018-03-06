<?php namespace SamPoyigi\Account\Components;

use Admin\Models\Addresses_model;
use Auth;

class AddressBook extends \System\Classes\BaseComponent
{
    public function onRun()
    {
        $this->page['addAddressEventHandler'] = $this->getEventHandler('onLoadAddForm');
        $this->page['submitAddressEventHandler'] = $this->getEventHandler('onSubmit');

        $this->page['customerAddresses'] = $this->loadAddressBook();
    }

    public function onLoadAddForm()
    {
        $this->pageCycle();

        $this->page['address'] = Addresses_model::make();

        return ['#address-book' => $this->renderPartial('@form')];
    }

    public function onSubmit()
    {

    }

    protected function loadAddressBook()
    {
        if (!$customer = Auth::customer())
            return [];

        return $customer->addresses()->get();
    }
}