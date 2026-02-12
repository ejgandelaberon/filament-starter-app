<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Model::shouldBeStrict(false);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => str('super-admin')->append(Config::string('app.domain'))->value(),
        ]);

        $this->call([
            RolePermissionSeeder::class,
            AccessControlSeeder::class,
        ]);
    }
}
