<?php

declare(strict_types=1);

namespace App\DataTable;

trait InteractsWithDataTable
{
    protected DataTable $dataTable;

    public function bootedInteractsWithDataTable(): void
    {
        $this->dataTable = $this->dataTable(
            DataTable::make($this)
        );
    }

    public function dataTable(DataTable $dataTable): DataTable
    {
        return $dataTable;
    }
}
