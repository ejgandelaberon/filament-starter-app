<?php

declare(strict_types=1);

namespace App\DataTable\Concerns;

use Closure;
use ReflectionClass;
use ReflectionMethod;

trait CollectsPublicGetters
{
    /**
     * @param  string[]  $exclude
     * @return Closure[]
     */
    protected function collectPublicGetters(array $exclude = []): array
    {
        $reflectionClass = new ReflectionClass($this);

        return collect($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method) => str($method->getName())->startsWith('get'))
            ->reject(fn (ReflectionMethod $method) => in_array($method->getName(), $exclude))
            ->mapWithKeys(fn (ReflectionMethod $method) => [
                $method->getName() => Closure::fromCallable([$this, $method->getName()]),
            ])
            ->all();
    }
}
