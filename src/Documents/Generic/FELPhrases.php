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
        $phrasesSubElems = [];
        $count             = 0;
        foreach ($this->phrases as $phrase) {
            $subElems = [
                PhraseXML::PhraseType->value   => $phrase->getPhraseType(),
                PhraseXML::codeScenario->value => $phrase->getCodeScenario(),
                PhraseXML::Resolution->value   => $phrase->getResolution(),
                PhraseXML::Date->value         => $phrase->getDate(),
            ];
            $phrasesSubElems[PhraseXML::TagSingular->value . (++$count)] = $subElems;
        }

        $xml = $this->buildXML(PhraseXML::TagPlural->value, element: $phrasesSubElems);

        return $xml;

    }
}
