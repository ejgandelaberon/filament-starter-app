<?php

declare(strict_types=1);

namespace App\DataTable;

use App\DataTable\DTO\DataTableSearch;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;

/**
 * @implements Arrayable<string, mixed>
 */
class Column implements Arrayable
{
    protected ?string $title = null;

    protected string $name;

    protected bool $searchable = true;

    protected bool $orderable = true;

    protected DataTableSearch $search;

    protected string|array|Closure|null $render = null; // @phpstan-ignore-line

    final private function __construct(protected string $data)
    {
        $this->search ??= DataTableSearch::default();
    }

    public static function make(string $data): static
    {
        return (new static($data))
            ->title(
                str($data)
                    ->title()
                    ->replace('_', ' ')
                    ->toString()
            )
            ->name($data);
    }

    /**
     * @param  array<string, mixed>  $column
     */
    public static function fromArray(array $column): static
    {
        return (new static($column['data']))
            ->title($column['title'] ?? null)
            ->name($column['name'])
            ->render($column['render'] ?? null)
            ->searchable($column['searchable'] === 'true')
            ->orderable($column['orderable'] === 'true')
            ->search(DataTableSearch::fromArray($column['search']));
    }

    public function toArray(): array
    {
        return [
            'data' => $this->getData(),
            'title' => $this->getTitle(),
            'name' => $this->getName(),
            'render' => $this->getRender(),
            'searchable' => $this->isSearchable(),
            'orderable' => $this->isOrderable(),
            'search' => $this->getSearch()->toArray(),
        ];
    }

    public function data(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function render(string|array|Closure|null $render): static // @phpstan-ignore-line
    {
        $this->render = $render;

        return $this;
    }

    public function getRender(): string|array|Closure|null // @phpstan-ignore-line
    {
        return $this->render;
    }

    /**
     * @throws PhpVersionNotSupportedException
     */
    public function searchable(bool $searchable = true, string|Closure|null $query = null): static
    {
        $this->searchable = $searchable;

        return match (true) {
            is_string($query) => $this->name($query),
            $query instanceof Closure => $this->searchUsing($query),
            default => $this,
        };
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function orderable(bool $orderable = true): static
    {
        $this->orderable = $orderable;

        return $this;
    }

    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    public function search(DataTableSearch $search): static
    {
        $this->search = $search;

        return $this;
    }

    public function getSearch(): DataTableSearch
    {
        return $this->search;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  Closure(Builder<Model>, ?string): Builder<Model>  $callback
     *
     * @throws PhpVersionNotSupportedException
     */
    public function searchUsing(Closure $callback): static
    {
        ColumnSearchManager::register($this->name, $callback);

        return $this;
    }

    /**
     * @return Closure(Builder<Model>, ?string): Builder<Model>|null
     */
    public function getSearchCallback(): ?Closure
    {
        return ColumnSearchManager::retrieve($this->name);
    }

    /**
     * @param  Builder<Model>  $query
     */
    public function applySearchCallback(Builder $query, ?string $value): void
    {
        $callback = $this->getSearchCallback();

        $callback && $callback($query, $value);
    }
}
