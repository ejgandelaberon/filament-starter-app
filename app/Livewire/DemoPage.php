<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Emsephron\TallDatatable\Columns\Column;
use Emsephron\TallDatatable\DataTable;
use Emsephron\TallDatatable\Enums\PagingType;
use Emsephron\TallDatatable\HasTallDatatable;
use Emsephron\TallDatatable\InteractsWithTallDatatable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DemoPage extends Component implements HasTallDatatable
{
    use InteractsWithTallDatatable;

    public function render(): Renderable
    {
        return view('livewire.demo-page');
    }

    public function dataTable(DataTable $dataTable): DataTable
    {
        return $dataTable
            ->errorMode(function (int $techNote, string $message): void {
                Log::error('Tall Datatable Error', compact('techNote', 'message'));
            })
            ->order([6, 'desc'])
            ->pagingType(PagingType::FULL)
            ->rowId('id')
            ->scrollY('600px')
            ->searchDelay(150)
            ->columns([
                Column::make('profile_photo_url')
                    ->title('Profile Photo')
                    ->renderUsing(static function (User $user, string $profilePhotoUrl): string {
                        return "
                            <div class='grid place-items-center'>
                                <img src='$profilePhotoUrl' class='w-8 h-8 rounded-full border border-sky-500' alt='Profile photo'>
                            </div>
                        ";
                    })
                    ->searchUsing(static function (Builder $query, ?string $search) {
                        return $query->orWhereLike('profile_photo_path', "%$search%");
                    })
                    ->orderable(false),

                Column::make('name')
                    ->renderUsing(static function (User $user, string $name): string {
                        return "<span class='text-sm whitespace-nowrap'>$name</span>";
                    }),

                Column::make('email')
                    ->renderUsing(static function (User $user, string $email): string {
                        return "
                            <a href='#' class='text-sm text-sky-600 hover:text-white hover:bg-sky-500 transition-colors duration-300 px-1.5 py-0.5 rounded whitespace-nowrap cursor-pointer'>
                                $email
                            </a>
                        ";
                    }),

                Column::make('email_verified_at')
                    ->renderUsing(static function (User $user, ?Carbon $emailVerifiedAt): string {
                        return "<span class='text-sm whitespace-nowrap'>{$emailVerifiedAt?->format('F j, Y')}</span>";
                    }),

                Column::make('created_at')
                    ->renderUsing(static function (User $user, ?Carbon $createdAt): string {
                        return "<span class='text-sm whitespace-nowrap'>{$createdAt?->format('F j, Y')}</span>";
                    }),

                Column::make('updated_at')
                    ->renderUsing(static function (User $user, ?Carbon $updatedAt): string {
                        return "<span class='text-sm whitespace-nowrap'>{$updatedAt?->format('F j, Y')}</span>";
                    }),

                Column::make('id')->title('ID'),
            ]);
    }

    public function query(): Builder
    {
        return User::query();
    }
}
