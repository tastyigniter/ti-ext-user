<?php

namespace Igniter\User\Tests\Classes;

use Igniter\User\Classes\BladeExtension;
use Illuminate\Support\Facades\Blade;
use Mockery;

it('registers custom directive', function() {
    Blade::shouldReceive('directive')->with('mainauth', Mockery::type('array'))->once();
    Blade::shouldReceive('directive')->with('endmainauth', Mockery::type('array'))->once();
    Blade::shouldReceive('directive')->with('adminauth', Mockery::type('array'))->once();
    Blade::shouldReceive('directive')->with('endadminauth', Mockery::type('array'))->once();

    $extension = new BladeExtension;
    $extension->register();
});

it('compiles mainauth directive', function() {
    $extension = new BladeExtension;
    $result = $extension->compilesMainAuth(null);

    expect($result)->toBe("<?php if(\Igniter\User\Facades\Auth::check()): ?>");
});

it('compiles adminauth directive', function() {
    $extension = new BladeExtension;
    $result = $extension->compilesAdminAuth(null);

    expect($result)->toBe("<?php if(\Igniter\User\Facades\AdminAuth::check()): ?>");
});

it('compiles endmainauth directive', function() {
    $extension = new BladeExtension;
    $result = $extension->compilesEndMainAuth();

    expect($result)->toBe('<?php endif ?>');
});

it('compiles endadminauth directive', function() {
    $extension = new BladeExtension;
    $result = $extension->compilesEndAdminAuth();

    expect($result)->toBe('<?php endif ?>');
});
