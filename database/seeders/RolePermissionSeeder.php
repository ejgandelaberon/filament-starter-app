<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SystemRoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class RolePermissionSeeder extends Seeder
{
    protected const array ACTIONS = [
        'view-any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force-delete',
    ];

    public function run(): void
    {
        $this->createRoles();
        $this->createPermissions();

        $permissions = Permission::all();

        Role::query()->where('name', SystemRoleEnum::SUPER_ADMIN)
            ->sole()
            ->givePermissionTo($permissions);
    }

    private function createRoles(): void
    {
        $roles = collect(SystemRoleEnum::cases())->map(fn (SystemRoleEnum $role) => [
            'name' => $role->value,
            'guard_name' => 'web',
        ])->toArray();

        Role::fillAndInsert($roles);
    }

    private function createPermissions(): void
    {
        $permissions = collect(self::ACTIONS)
            ->flatMap(fn (string $action) => collect($this->getResources())->map(fn (string $resource) => [
                'name' => "{$action}-{$resource}",
                'guard_name' => 'web',
            ]))
            ->toArray();

        Permission::fillAndInsert($permissions);
    }

    /**
     * @return array<int, string>
     */
    protected function getResources(): array
    {
        /** @var array<int, string> $resources */
        $resources = [];
        $modelPath = app_path('Models');
        $namespace = 'App\\Models\\';

        foreach (File::files($modelPath) as $file) {
            if ($file->getExtension() === 'php') {
                $class = $namespace.$file->getFilenameWithoutExtension();
                if (class_exists($class) && is_subclass_of($class, Model::class)) {
                    $resources[] = str($class)
                        ->classBasename()
                        ->kebab()
                        ->toString();
                }
            }
        }

        return $resources;
    }
}
