<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * @var array<int, array{slug: string, name: string, description: string}>
     */
    public const ROLES = [
        [
            'slug' => 'super_admin',
            'name' => 'Super Admin',
            'description' => 'Full unrestricted access to the entire system. Only super admins can create, edit, deactivate, or delete other super admin users.',
        ],
        [
            'slug' => 'admin',
            'name' => 'Admin',
            'description' => 'Full admin dashboard access for day-to-day operations. Cannot see or modify super admin accounts and cannot promote anyone to super admin.',
        ],
        [
            'slug' => 'manager',
            'name' => 'Manager',
            'description' => 'Oversees teams and content. Currently sees only the Overview page until manager-specific features are added.',
        ],
        [
            'slug' => 'user',
            'name' => 'User',
            'description' => 'Standard authenticated user. Sees only the Overview page in the dashboard; everything else is system-only.',
        ],
        [
            'slug' => 'viewer',
            'name' => 'Viewer',
            'description' => 'Read-only auditor. Can view scheduler runs and Laravel logs but cannot clear or modify any data.',
        ],
        [
            'slug' => 'service_account',
            'name' => 'Service Account',
            'description' => 'Non-human account used by external applications and APIs. Cannot sign in to the web dashboard.',
        ],
        [
            'slug' => 'operator',
            'name' => 'Operator',
            'description' => 'Operational role: scheduler, logs, and queue management. Can clear scheduler history and log files. Cannot manage users or system configuration.',
        ],
        [
            'slug' => 'support',
            'name' => 'Support',
            'description' => 'Assists end-users. Currently sees only the Overview page; read-only user lookup is planned.',
        ],
    ];

    public function run(): void
    {
        foreach (self::ROLES as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name'], 'description' => $role['description']],
            );
        }

        $superAdmin = Role::query()->where('slug', 'super_admin')->first();

        if ($superAdmin) {
            User::query()
                ->where('email', MasterUserSeeder::EMAIL)
                ->update(['role_id' => $superAdmin->id, 'is_active' => true]);
        }
    }
}
