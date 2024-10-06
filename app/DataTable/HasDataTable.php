<?php

declare(strict_types=1);

namespace App\DataTable;

use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Livewire\Attributes\Computed;

trait HasDataTable
{
    /**
     * @throws PhpVersionNotSupportedException
     */
    #[Computed(persist: true)]
    public function dataTable(): DataTable
    {
        return $this->configureDataTable(
            DataTable::make()->livewire($this)
        );
    }

    abstract public function configureDataTable(DataTable $dataTable): DataTable;
}
