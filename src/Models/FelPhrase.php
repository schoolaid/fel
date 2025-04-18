<?php

namespace Schoolaid\Fel\Models;

class FelPhrase
{
    public string $scenarioCode;
    public string $phraseType;
    
    public function __construct(
        string $scenarioCode = '1',
        string $phraseType = '1'
    ) {
        $this->scenarioCode = $scenarioCode;
        $this->phraseType = $phraseType;
    }
    
    public function toArray(): array
    {
        return [
            'scenarioCode' => $this->scenarioCode,
            'phraseType' => $this->phraseType
        ];
    }
} 