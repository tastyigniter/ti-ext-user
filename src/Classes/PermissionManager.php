<?php

declare(strict_types=1);

namespace Igniter\User\Classes;

use Igniter\System\Classes\ExtensionManager;

class PermissionManager
{
    protected $permissions = [];

    /**
     * @var array A cache of permissions.
     */
    protected $permissionCache = [];

    /**
     * @var array Cache of registration callbacks.
     */
    protected $callbacks = [];

    protected static $permissionDefaults = [
        'code' => null,
        'label' => null,
        'group' => 'Undefined group',
        'description' => null,
        'priority' => 999,
    ];

    /**
     * Returns a list of the registered permissions.
     * @return array
     */
    public function listPermissions()
    {
        if ($this->permissionCache) {
            return $this->permissionCache;
        }

        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        $permissionBundles = resolve(ExtensionManager::class)->getRegistrationMethodValues('registerPermissions');
        foreach ($permissionBundles as $owner => $permissionBundle) {
            if (!is_array($permissionBundle)) {
                continue;
            }

            $this->registerPermissions($owner, $permissionBundle);
        }

        usort($this->permissions, fn($a, $b): int => $a->priority <=> $b->priority);

        return $this->permissionCache = $this->permissions;
    }

    public function listGroupedPermissions()
    {
        $grouped = [];

        foreach ($this->listPermissions() as $permission) {
            $group = strtolower(strlen((string)$permission->group) !== 0 ? $permission->group : 'Undefined group');

            $permission->group ??= $group;

            if (!array_key_exists($group, $grouped)) {
                $grouped[$group] = [];
            }

            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    public function checkPermission($permissions, $checkPermissions, $checkAll): bool
    {
        foreach ($checkPermissions as $permission) {
            $matched = $this->isPermissionGranted((string)$permission, $permissions);

            if ($checkAll === false && $matched) {
                return true;
            }

            if ($checkAll === true && $matched === false) {
                return false;
            }
        }

        return $checkAll !== false;
    }

    protected function isPermissionGranted(string $permission, $permissions): bool
    {
        foreach ($permissions as $groupPermission => $permitted) {
            if ($permitted == 1 && $this->permissionMatches((string)$groupPermission, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Permission names are compared exactly. A prefix or suffix match is only
     * allowed when one side carries an explicit leading or trailing wildcard,
     * e.g. a grant of "Admin.*" covers "Admin.Orders", and a check for
     * "Igniter.Pages.*" is satisfied by a grant of "Igniter.Pages.Manage".
     * Names that merely share a prefix or suffix, such as "Admin.Impersonate"
     * and "Admin.ImpersonateCustomers", never satisfy each other.
     */
    protected function permissionMatches(string $grantedPermission, string $checkPermission): bool
    {
        if ($grantedPermission === $checkPermission) {
            return true;
        }

        return $this->matchesWildcard($grantedPermission, $checkPermission)
            || $this->matchesWildcard($checkPermission, $grantedPermission);
    }

    protected function matchesWildcard(string $pattern, string $permission): bool
    {
        if (strlen($pattern) < 2) {
            return false;
        }

        if (str_ends_with($pattern, '*')) {
            return str_starts_with($permission, substr($pattern, 0, -1));
        }

        if (str_starts_with($pattern, '*')) {
            return str_ends_with($permission, substr($pattern, 1));
        }

        return false;
    }

    //
    // Registration
    //

    public function registerPermissions($owner, array $definitions): void
    {
        foreach ($definitions as $code => $definition) {
            if (!isset($definition['label']) && isset($definition['description'])) {
                $definition['label'] = $definition['description'];
                unset($definition['description']);
            }

            $permission = (object)array_merge(self::$permissionDefaults, array_merge([
                'code' => $code,
                'owner' => $owner,
            ], $definition));

            $this->permissions[] = $permission;
        }
    }

    /**
     * Registers a callback function that defines permissions.
     * The callback function should register permissions by calling the manager's
     * registerPermissions() function. This instance is passed to the
     * callback function as an argument. Usage:
     * <pre>
     *   AdminAuth::registerCallback(function($manager){
     *       $manager->registerPermissions([...]);
     *   });
     * </pre>
     *
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback): void
    {
        $this->callbacks[] = $callback;
    }
}
