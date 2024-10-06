<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
class AjaxSearch implements Arrayable
{
    /**
     * @param  array<array-key, mixed>  $fixed
     */
    public function __construct(
        public ?string $value,
        public bool $regex,
        public array $fixed = [],
    ) {}

    /**
     * @param  array{ value: ?string, regex: bool, fixed?: array<array-key, mixed> }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            regex: $data['regex'] == 'true',
            fixed: $data['fixed'] ?? [],
        );
    }

    public static function default(): self
    {
        return new self(
            value: null,
            regex: false,
            fixed: [],
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'regex' => $this->regex,
            'fixed' => $this->fixed,
        ];
    }
}
