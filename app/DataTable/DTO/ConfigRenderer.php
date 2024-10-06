<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, ?string>
 */
readonly class ConfigRenderer implements Arrayable
{
    public function __construct(
        public ?string $header = null,
        public ?string $layout = null,
        public ?string $pagingButton = null,
        public ?string $pagingContainer = null,
    ) {}

    /**
     * @return array<string, ?string>
     */
    public function toArray(): array
    {
        return [
            'header' => $this->header,
            'layout' => $this->layout,
            'pagingButton' => $this->pagingButton,
            'pagingContainer' => $this->pagingContainer,
        ];
    }
}
