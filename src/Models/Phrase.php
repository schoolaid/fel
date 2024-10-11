<?php
namespace SchoolAid\FEL\Models;

class Phrase
{
    public function __construct(
        private string $phraseType,
        private string $codeScenario,
        private ?string $resolution,
        private ?string $date
    )
    {}

    public function getPhraseType(): string
    {
        return $this->phraseType;
    }

    public function getCodeScenario(): string
    {
        return $this->codeScenario;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }
}