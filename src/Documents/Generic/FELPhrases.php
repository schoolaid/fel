<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\PhraseXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELPhrases implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $phrases
    ) {}

    public function asXML(): string
    {
        $phrasesAttributes = [];
        $count             = 0;
        foreach ($this->phrases as $phrase) {
            $attributes = [
                PhraseXML::PhraseType->value   => $phrase->getPhraseType(),
                PhraseXML::codeScenario->value => $phrase->getCodeScenario(),
                PhraseXML::Resolution->value   => $phrase->getResolution(),
                PhraseXML::Date->value         => $phrase->getDate(),
            ];
            $phrasesAttributes[PhraseXML::Tag->value . (++$count)] = $attributes;
        }

        $xml = $this->buildXML(PhraseXML::Tag->value, $phrasesAttributes);

        return $xml;

    }
}
