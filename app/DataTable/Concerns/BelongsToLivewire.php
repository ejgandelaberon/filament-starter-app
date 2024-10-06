<?php

declare(strict_types=1);

namespace App\DataTable\Concerns;

use Livewire\Component;

trait BelongsToLivewire
{
    protected Component $livewire;

    public function livewire(Component $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function getLivewire(): Component
    {
        return $this->livewire;
    }
}
