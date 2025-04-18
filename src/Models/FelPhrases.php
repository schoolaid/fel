<?php

namespace Schoolaid\Fel\Models;

class FelPhrases
{
    /**
     * @var FelPhrase[]
     */
    public array $phrases = [];
    
    public function __construct(array $phrases = [])
    {
        foreach ($phrases as $phrase) {
            if ($phrase instanceof FelPhrase) {
                $this->phrases[] = $phrase;
            } else {
                $this->phrases[] = new FelPhrase(
                    $phrase['scenarioCode'] ?? '1',
                    $phrase['phraseType'] ?? '1'
                );
            }
        }
    }
    
    public function addPhrase(FelPhrase $phrase): self
    {
        $this->phrases[] = $phrase;
        return $this;
    }
    
    public function toArray(): array
    {
        return array_map(fn(FelPhrase $phrase) => $phrase->toArray(), $this->phrases);
    }
} 