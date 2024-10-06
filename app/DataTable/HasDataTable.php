<?php

declare(strict_types=1);

namespace App\DataTable;

use Illuminate\Database\Eloquent\Model;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Livewire\Attributes\Computed;

trait HasDataTable
{
    /**
     * @return class-string<Model>
     */
    abstract public function model(): string;

    /**
     * @throws PhpVersionNotSupportedException
     */
    #[Computed(persist: true)]
    public function dataTable(): DataTable
    {
        return $this->configureDataTable(
            DataTable::make($this->model())->livewire($this)
        );
    }

    abstract public function configureDataTable(DataTable $dataTable): DataTable;
}
