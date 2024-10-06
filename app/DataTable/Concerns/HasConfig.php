<?php

declare(strict_types=1);

namespace App\DataTable\Concerns;

use App\DataTable\Column;

trait HasConfig
{
    /**
     * @var array<array-key, mixed>
     */
    protected array $data = [];

    /**
     * @var Column[]
     */
    protected array $columns = [];

    protected ?string $ajax = null;

    protected ?string $getRecordsUsing = null;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public function data(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  Column[]  $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getColumns(): array
    {
        return array_map(fn (Column $column): array => $column->toArray(), $this->columns);
    }

    public function getRecordsUsing(string $componentMethodName): static
    {
        $this->getRecordsUsing = $componentMethodName;

        return $this;
    }

    public function getGetRecordsUsing(): ?string
    {
        return $this->getRecordsUsing;
    }

    public function ajax(?string $ajax): static
    {
        $this->ajax = $ajax;

        return $this;
    }

    public function getAjax(): ?string
    {
        return $this->ajax;
    }
}
