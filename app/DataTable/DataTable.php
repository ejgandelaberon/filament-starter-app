<?php

declare(strict_types=1);

namespace App\DataTable;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Livewire\Component;

/**
 * @implements Arrayable<string, mixed>
 */
class DataTable implements Arrayable, Htmlable
{
    use Concerns\BelongsToLivewire;
    use Concerns\CollectsPublicGetters;
    use Concerns\HasConfig;

    final private function __construct(protected Component $livewire)
    {
        //
    }

    public static function make(Component $livewire): static
    {
        return new static($livewire);
    }

    public function render(): Renderable
    {
        return view('components.datatable', $this->collectPublicGetters(exclude: [
            'getModel',
            'getSerializedCallbacks',
        ]));
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
