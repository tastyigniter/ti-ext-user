<?php

namespace Tests\Requests;

use Igniter\User\Requests\CustomerGroupRequest;

it('has rules for group_name field', function () {
    $rules = array_get((new CustomerGroupRequest)->rules(), 'group_name');

    expect('required')->toBeIn($rules)
        ->and('between:2,32')->toBeIn($rules);
});

it('has rules for description field', function () {
    $rules = array_get((new CustomerGroupRequest)->rules(), 'description');

    expect('string')->toBeIn($rules)
        ->and('between:2,512')->toBeIn($rules);
});
