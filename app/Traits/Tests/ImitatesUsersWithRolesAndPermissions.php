<?php

namespace App\Traits\Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait ImitatesUsersWithRolesAndPermissions
{
    use InteractsWithAuthentication;

    public function beUserWithRoles(...$roles)
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        foreach ($roles as $role) {
            $role = Role::findOrCreate($role);

            $user->assignRole($role);
        }

        $this->be($user);
    }

    public function beUserWithPermissions(...$permissions)
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        foreach ($permissions as $permission) {
            $permission = Permission::findOrCreate($permission);

            $user->givePermissionTo($permission);
        }

        $this->be($user);
    }
}
