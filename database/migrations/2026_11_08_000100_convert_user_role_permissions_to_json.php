<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * User role permissions were stored as PHP serialized arrays and read with unserialize().
 * Convert them to JSON so they are read with json_decode() and can never instantiate PHP objects.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('admin_user_roles')->orderBy('user_role_id')->chunkById(500, function($roles): void {
            foreach ($roles as $role) {
                $permissions = $this->legacyToJson($role->permissions);
                if ($permissions !== $role->permissions) {
                    DB::table('admin_user_roles')
                        ->where('user_role_id', $role->user_role_id)
                        ->update(['permissions' => $permissions]);
                }
            }
        }, 'user_role_id');
    }

    protected function legacyToJson(?string $value): ?string
    {
        // Empty values and values already stored as JSON, such as those written by
        // the initial seeder on a fresh install, are left as they are.
        if (in_array($value, [null, ''], true) || json_validate($value)) {
            return $value;
        }

        // Values that cannot be unserialized, or that contain objects, are dropped.
        $decoded = @unserialize($value, ['allowed_classes' => false]);
        if (($decoded === false && $value !== 'b:0;') || is_object($decoded) || (is_array($decoded) && $this->containsObject($decoded))) {
            return null;
        }

        return json_encode($decoded, JSON_THROW_ON_ERROR);
    }

    protected function containsObject(array $value): bool
    {
        foreach ($value as $item) {
            if (is_object($item) || (is_array($item) && $this->containsObject($item))) {
                return true;
            }
        }

        return false;
    }
};
