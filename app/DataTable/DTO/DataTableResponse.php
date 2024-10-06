<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use App\DataTable\Column;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @implements Arrayable<string, mixed>
 */
class DataTableResponse implements Arrayable
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public int $draw,
        public array $data,
        public int $recordsTotal,
        public int $recordsFiltered,
    ) {}

    public function toArray(): array
    {
        return [
            'draw' => $this->draw,
            'data' => $this->data,
            'recordsTotal' => $this->recordsTotal,
            'recordsFiltered' => $this->recordsFiltered,
        ];
    }

    /**
     * @param  class-string<Model>  $model
     */
    public static function make(DataTableRequest $request, string $model): self
    {
        /** @var Builder<Model> $query */
        $query = $model::query();

        static::applySearch($query, $request);
        static::applyOrdering($query, $request);

        // dd($query->toRawSql());

        $paginator = $query->paginate(
            perPage: $request->length,
            page: $request->start / $request->length + 1
        );

        return new self(
            draw: $request->draw,
            data: $paginator->items(),
            recordsTotal: $paginator->total(),
            recordsFiltered: $paginator->total(),
        );
    }

    /**
     * @param  Builder<Model>  $query
     */
    protected static function applySearch(Builder $query, DataTableRequest $request): void
    {
        if ($request->search->value) {
            foreach ($request->columns as $column) {
                if (! $column->isSearchable()) {
                    continue;
                }

                if ($column->getSearchCallback()) {
                    $column->applySearchCallback($query, $request->search->value);

                    continue;
                }

                $query->orWhereLike($column->getName(), "%{$request->search->value}%");
            }
        }
    }

    /**
     * @param  Builder<Model>  $query
     */
    protected static function applyOrdering(Builder $query, DataTableRequest $request): void
    {
        if ($request->order) {
            $orderableColumns = static::orderableColumns($request);

            foreach ($request->order as $order) {
                $column = strval(
                    Arr::first($orderableColumns, fn (string $column): bool => $column === $order->name)
                );
                $direction = $order->dir;

                $query->orderBy($column, $direction);
            }
        }
    }

    /**
     * @return string[]
     */
    protected static function orderableColumns(DataTableRequest $request): array
    {
        /** @var string[] $orderableColumns */
        $orderableColumns = collect($request->columns)
            ->filter(fn (Column $column): bool => $column->isOrderable())
            ->map(fn (Column $column): ?string => $column->getData())
            ->toArray();

        return $orderableColumns;
    }
}
