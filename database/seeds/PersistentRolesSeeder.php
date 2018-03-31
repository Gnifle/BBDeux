<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PersistentRolesSeeder extends Seeder
{
    public function run()
    {
        $gnifle = User::create([
            'name' => 'Gnifle',
            'email' => 'bbdeux@gnifle.com',
            'password' => '$2y$10$X7O90M1fj5hXVfBQK0jwxOMxsCsbaaOz2/LAOfZi/kbbb0sdRANLC', // 'secret'
        ]);

        /** @var Role $admin */
        $admin = Role::create([
            'name' => 'admin',
        ]);

        $gnifle->assignRole($admin);
    }
}
