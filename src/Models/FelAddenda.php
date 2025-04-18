<?php

namespace Schoolaid\Fel\Models;

class FelAddenda
{
    public string $namespace;
    public string $name;
    public string $value;

    public function __construct(
        string $namespace = 'https://www.sat.gob.gt/fel/addenda',
        string $name = 'FelAddenda',
        string $value = 'FelAddenda'
    )
    {
        $this->namespace = $namespace;
        $this->name = $name;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'namespace' => $this->namespace,
            'name' => $this->name,
            'value' => $this->value
        ];
    }
} 