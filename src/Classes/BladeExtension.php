<?php

namespace Igniter\User\Classes;

use Illuminate\Support\Facades\Blade;

class BladeExtension
{
    public function register()
    {
        Blade::directive('mainauth', [$this, 'compilesMainAuth']);
        Blade::directive('endmainauth', [$this, 'compilesEndMainAuth']);
        Blade::directive('adminauth', [$this, 'compilesAdminAuth']);
        Blade::directive('endadminauth', [$this, 'compilesEndAdminAuth']);
    }

    public function compilesMainAuth($expression)
    {
        return "<?php if(\Igniter\User\Facades\Auth::check()): ?>";
    }

    public function compilesAdminAuth($expression)
    {
        return "<?php if(\Igniter\User\Facades\AdminAuth::check()): ?>";
    }

    public function compilesEndMainAuth()
    {
        return '<?php endif ?>';
    }

    public function compilesEndAdminAuth()
    {
        return '<?php endif ?>';
    }
}