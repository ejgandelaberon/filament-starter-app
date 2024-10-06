<?php

declare(strict_types=1);

namespace App\DataTable\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
class DataTableSearch implements Arrayable
{
    public function __construct(
        public ?string $value,
        public bool $regex,
    ) {}

    /**
     * @param  array{ value: ?string, regex: bool }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            regex: $data['regex'] == 'true',
        );
    }

    public static function default(): self
    {
        return new self(
            value: null,
            regex: false,
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'regex' => $this->regex,
        ];
    }
}
