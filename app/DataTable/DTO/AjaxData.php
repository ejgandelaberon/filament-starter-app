<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use App\DataTable\Column;
use Illuminate\Contracts\Support\Arrayable;

class AjaxData implements Arrayable
{
    /**
     * @param  Column[]  $columns
     * @param  AjaxOrder[]  $order
     */
    final public function __construct(
        public int $draw,
        public ?int $start = null,
        public ?int $length = null,
        public array $columns = [],
        public array $order = [],
        public ?AjaxSearch $search = null,
    ) {}

    public static function make(int $draw): self
    {
        return new self($draw);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'draw' => $this->draw,
            'start' => $this->start,
            'length' => $this->length,
            'columns' => collect($this->columns)->map(fn (Column $column) => $column->toArray())->all(),
            'order' => collect($this->order)->map(fn (AjaxOrder $order) => $order->toArray())->all(),
            'search' => $this->search?->toArray(),
        ];
    }

    public function getDraw(): int
    {
        return $this->draw;
    }

    public function draw(int $draw): static
    {
        $this->draw = $draw;

        return $this;
    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function start(?int $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function length(?int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
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
     * @return AjaxOrder[]
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @param  AjaxOrder[]  $order
     */
    public function order(array $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getSearch(): ?AjaxSearch
    {
        return $this->search;
    }

    public function search(?AjaxSearch $search): static
    {
        $this->search = $search;

        return $this;
    }
}
