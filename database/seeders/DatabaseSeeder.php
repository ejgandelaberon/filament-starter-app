<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SystemRoleEnum;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $domain = Config::string('app.domain');

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => "superadmin$domain",
        ]);

        User::factory()->count(10)->create();

        foreach (SystemRoleEnum::values() as $role) {
            Role::create(['name' => $role]);
        }

        Artisan::call('shield:generate', [
            '--all' => true,
            '--ignore-existing-policies' => true,
        ]);

        Artisan::call('shield:super-admin', [
            '--user' => 1,
        ]);

        User::whereNot('id', 1)->each(function (User $user) {
            $user->assignRole(
                Role::whereNot('name', SystemRoleEnum::SUPER_ADMIN->value)
                    ->inRandomOrder()
                    ->firstOrFail()
            );
        });
    }
}
