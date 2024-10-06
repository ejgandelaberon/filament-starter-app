<?php

declare(strict_types=1);

namespace App\DataTable;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

/**
 * @implements Arrayable<string, mixed>
 */
class DataTable implements Arrayable, Htmlable
{
    use Concerns\BelongsToLivewire;
    use Concerns\HasConfig;

    final private function __construct()
    {
        //
    }

    public static function make(): static
    {
        return new static;
    }

    public function render(): Renderable
    {
        return view('components.datatable', [
            'livewireId' => $this->getLivewire(),
            'data' => $this->getData(),
            'columns' => $this->getColumns(),
            'ajax' => $this->getAjax(),
            'getRecordsUsing' => $this->getGetRecordsUsing(),
        ]);
    }

    public function toArray(): array
    {
        return [
            'data' => $this->getData(),
            'columns' => $this->getColumns(),
        ];
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }
}
