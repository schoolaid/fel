<?php

namespace Schoolaid\Fel\Traits;

trait Makeable
{
    /**
     * Create a new instance of the class.
     *
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}