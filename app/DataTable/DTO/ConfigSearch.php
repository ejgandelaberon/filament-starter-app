<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, bool|string|null>
 */
readonly class ConfigSearch implements Arrayable
{
    public function __construct(
        public bool $caseInsensitive = true,
        public bool $regex = false,
        public bool $smart = false,
        public ?string $search = null,
        public ?string $searchPlaceholder = null,
    ) {}

    public function toArray(): array
    {
        return [
            'caseInsensitive' => $this->caseInsensitive,
            'regex' => $this->regex,
            'smart' => $this->smart,
            'search' => $this->search,
            'searchPlaceholder' => $this->searchPlaceholder,
        ];
    }
}
