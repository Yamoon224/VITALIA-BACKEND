<?php

namespace Database\Seeders;

use App\Domains\Identity\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Crée les rôles du système (section 4) avant tout autre seeder : un compte
 * ne peut recevoir un rôle qui n'existe pas encore (`assignRole()` échoue).
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserRole::cases() as $role) {
            Role::findOrCreate($role->value);
        }
    }
}
