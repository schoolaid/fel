<?php

namespace SchoolAid\FEL\Models;

class Addenda 
{
    public function __construct(
        private string $value,
        private string $name
    )
    {}

    public function getValue(): string
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}