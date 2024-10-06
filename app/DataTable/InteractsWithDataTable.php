<?php

declare(strict_types=1);

namespace App\DataTable;

use App\DataTable\DTO\AjaxData;
use App\DataTable\DTO\AjaxOrder;
use App\DataTable\DTO\AjaxSearch;
use App\DataTable\DTO\Response;
use Illuminate\Database\Eloquent\Builder;

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

    public function fetch(array $data): array
    {
        $ajaxData = AjaxData::make($data['draw'])
            ->start($data['start'])
            ->length($data['length'])
            ->columns($this->dataTable->getColumns(false))
            ->order(array_map(fn (array $order) => AjaxOrder::fromArray($order), $data['order']))
            ->search(AjaxSearch::fromArray($data['search']));

        return Response::make($ajaxData, $this->query())->toArray();
    }

    abstract public function query(): Builder;
}
