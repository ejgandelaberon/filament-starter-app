<?php

declare(strict_types=1);

namespace App\DataTable;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;

class ColumnSearchManager
{
    /**
     * @param  Closure(Builder<Model>, ?string): Builder<Model>  $callback
     *
     * @throws PhpVersionNotSupportedException
     */
    public static function register(string $column, Closure $callback): void
    {
        Session::put("search.$column", serialize(new SerializableClosure($callback)));
    }

    /**
     * @return Closure(Builder<Model>, ?string): Builder<Model>|null
     */
    public static function retrieve(string $column): ?Closure
    {
        /** @var string|null $callback */
        $callback = Session::get("search.$column");

        return $callback ? unserialize($callback)->getClosure() : null;
    }
}
