<?php

use Igniter\User\Models\User;
use Illuminate\Http\Request;

uses(\SamPoyigi\Testbench\TestCase::class)->in(__DIR__);

function actingAsSuperUser(?User $user = null)
{
    return test()->actingAs($user ?? User::factory()->superUser()->create(), 'igniter-admin');
}

function setObjectProtectedProperty($object, $property, $value)
{
    $reflection = new \ReflectionClass($object);
    $property = $reflection->getProperty($property);
    $property->setAccessible(true);
    $property->setValue($object, $value);
}

function getObjectProtectedProperty($object, $property)
{
    $reflection = new \ReflectionClass($object);
    $property = $reflection->getProperty($property);
    $property->setAccessible(true);

    return $property->getValue($object);
}

function mockRequest(array $data)
{
    $mockRequest = Mockery::mock(Request::class)->makePartial();
    $mockRequest->shouldReceive('post')->andReturn($data);
    $mockRequest->shouldReceive('setUserResolver')->andReturnNull();
    app()->instance('request', $mockRequest);

    return $mockRequest;
}
