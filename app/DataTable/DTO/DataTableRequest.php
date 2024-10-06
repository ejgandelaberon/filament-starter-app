<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use App\DataTable\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DataTableRequest
{
    /**
     * @param  Column[]  $columns
     * @param  DataTableOrder[]  $order
     */
    public function __construct(
        public int $draw,
        public int $start,
        public int $length,
        public array $columns,
        public array $order,
        public DataTableSearch $search,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            draw: $request->integer('draw'),
            start: $request->integer('start'),
            length: $request->integer('length'),
            columns: self::createColumns($request),
            order: self::createOrders($request),
            search: self::createDataTableSearch($request),
        );
    }

    /**
     * @return Column[]
     */
    protected static function createColumns(Request $request): array
    {
        return collect(Arr::wrap($request->input('columns')))
            ->map(fn (array $column): Column => Column::fromArray($column))
            ->all();
    }

    protected static function createDataTableSearch(Request $request): DataTableSearch
    {
        return DataTableSearch::fromArray([
            'value' => $request->string('search.value')->toString(),
            'regex' => $request->input('search.regex') === 'true',
        ]);
    }

    /**
     * @return DataTableOrder[]
     */
    protected static function createOrders(Request $request): array
    {
        return collect(Arr::wrap($request->input('order')))
            ->map(fn (array $order): DataTableOrder => DataTableOrder::fromArray($order))
            ->all();
    }
}
