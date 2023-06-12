<?php

namespace Igniter\User\Classes;

class BladeExtension
{
    public function __invoke($compiler)
    {
        $compiler->directive('mainauth', [$this, 'compilesMainAuth']);
        $compiler->directive('endmainauth', [$this, 'compilesEndMainAuth']);
        $compiler->directive('adminauth', [$this, 'compilesAdminAuth']);
        $compiler->directive('endadminauth', [$this, 'compilesEndAdminAuth']);
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