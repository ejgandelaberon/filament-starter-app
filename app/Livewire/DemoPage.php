<?php

declare(strict_types=1);

namespace App\Livewire;

use App\DataTable\Column;
use App\DataTable\DataTable;
use App\DataTable\Enums\PagingType;
use App\DataTable\InteractsWithDataTable;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DemoPage extends Component
{
    use InteractsWithDataTable;

    public function render(): Renderable
    {
        return view('livewire.demo-page');
    }

    public function dataTable(DataTable $dataTable): DataTable
    {
        return $dataTable
            ->order([6, 'desc'])
            ->pagingType(PagingType::FULL)
            ->rowId('id')
            ->scrollY('600px')
            ->searchDelay(150)
            ->columns([
                Column::make('name'),
                Column::make('email'),
                Column::make('email_verified_at'),
                Column::make('created_at'),
                Column::make('updated_at'),
                Column::make('profile_photo_url')
                    ->searchUsing(function (Builder $query, ?string $search) {
                        return $query->orWhereLike('profile_photo_path', "%$search%");
                    })
                    ->orderable(false),
                Column::make('id'),
            ]);
    }

    public function query(): Builder
    {
        return User::query();
    }
}
