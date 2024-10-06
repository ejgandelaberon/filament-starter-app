<?php

declare(strict_types=1);

namespace App\DataTable;

use App\DataTable\DTO\AjaxSearch;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements Arrayable<string, mixed>
 */
class Column implements Arrayable
{
    protected ?string $title = null;

    protected string $name;

    protected bool $searchable = true;

    protected bool $orderable = true;

    protected AjaxSearch $search;

    protected string|array|Closure|null $render = null; // @phpstan-ignore-line

    protected ?Closure $searchCallback = null;

    final private function __construct(protected string $data)
    {
        $this->search ??= AjaxSearch::default();
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
     * @param  array{
     *     data: string,
     *     title?: string,
     *     name: string,
     *     render: string|string[]|Closure|null,
     *     searchable: bool,
     *     orderable: bool,
     *     search: array{ value: ?string, regex: bool, fixed?: array<array-key, mixed> }
     * }  $column
     */
    public static function fromArray(array $column): static
    {
        return (new static($column['data']))
            ->title($column['title'] ?? null)
            ->name($column['name'])
            ->render($column['render'] ?? null)
            ->searchable($column['searchable'])
            ->orderable($column['orderable'])
            ->search(AjaxSearch::fromArray($column['search']));
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

    public function search(AjaxSearch $search): static
    {
        $this->search = $search;

        return $this;
    }

    public function getSearch(): AjaxSearch
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
     */
    public function searchUsing(Closure $callback): static
    {
        $this->searchCallback = $callback;

        return $this;
    }

    public function getSearchCallback(): ?Closure
    {
        return $this->searchCallback;
    }
}
