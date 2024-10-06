<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use App\DataTable\Column;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;

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

    public static function make(DataTableRequest $request): self
    {
        /** @var Builder<Model> $query */
        $query = $request->model::query();

        static::applySearch($query, $request);
        static::applyOrdering($query, $request);

        $paginator = static::paginate($query, $request);

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

                $columnSearchCallback = $request->columnSearch[$column->getData()] ?? null;

                if ($columnSearchCallback !== null) {
                    $callback = static::deserializeCallback($columnSearchCallback);
                    $callback($query, $request->search->value);

                    continue;
                }

                $query->orWhereLike($column->getName(), "%{$request->search->value}%");
            }
        }
    }

    /**
     * @return Closure(Builder<Model>, ?string): Builder<Model>
     */
    protected static function deserializeCallback(string $callback): Closure
    {
        /** @var SerializableClosure $callback */
        $callback = unserialize($callback);

        try {
            return $callback->getClosure();
        } catch (PhpVersionNotSupportedException $e) {
            report($e);

            return fn (Builder $query, ?string $search): Builder => $query;
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

    /**
     * @param  Builder<Model>  $query
     * @return LengthAwarePaginator<Model>
     */
    protected static function paginate(Builder $query, DataTableRequest $request): LengthAwarePaginator
    {
        $length = $request->length;
        $page = $request->start / $request->length + 1;

        if ($length === -1) {
            $length = $query->count();
            $page = 1;
        }

        return $query->paginate(perPage: $length, page: $page);
    }
}
