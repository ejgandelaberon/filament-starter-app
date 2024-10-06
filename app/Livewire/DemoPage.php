<?php

declare(strict_types=1);

namespace App\Livewire;

use App\DataTable\Column;
use App\DataTable\DataTable;
use App\DataTable\Enums\PagingType;
use App\DataTable\HasDataTable;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DemoPage extends Component
{
    use HasDataTable;

    public function render(): Renderable
    {
        return view('livewire.demo-page');
    }

    public function model(): string
    {
        return User::class;
    }

    /**
     * @throws PhpVersionNotSupportedException
     */
    public function configureDataTable(DataTable $dataTable): DataTable
    {
        return $dataTable
            ->ajax(route('data'))
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

    public function asyncData(): array // @phpstan-ignore-line
    {
        sleep(2);

        return require app_path('DataTable/data/data.php');
    }
}
