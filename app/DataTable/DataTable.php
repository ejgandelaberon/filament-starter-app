<?php

declare(strict_types=1);

namespace App\DataTable;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements Arrayable<string, mixed>
 */
class DataTable implements Arrayable, Htmlable
{
    use Concerns\BelongsToLivewire;
    use Concerns\CollectsPublicGetters;
    use Concerns\HasConfig;

    /**
     * @param  class-string<Model>  $model
     */
    final private function __construct(protected string $model)
    {
        //
    }

    /**
     * @param  class-string<Model>  $model
     */
    public static function make(string $model): static
    {
        return new static($model);
    }

    public function render(): Renderable
    {
        //        dd($this->collectPublicGetters(exclude: [
        //            'getModel',
        //            'getSerializedCallbacks',
        //        ]));
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

    /**
     * @param  class-string<Model>  $model
     */
    public function model(string $model): DataTable
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }
}
