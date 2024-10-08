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

        foreach ($this->phrases as $phrase) {
            $attributes = [
                PhraseXML::codeScenario->value => $phrase->getCodeScenario(),
                PhraseXML::PhraseType->value   => $phrase->getPhraseType(),
                PhraseXML::Resolution->value   => $phrase->getResolution(),
                PhraseXML::Date->value         => $phrase->getDate(),
            ];
    
            $phrasesSubElems[] = $this->buildXML(PhraseXML::TagSingular->value, $attributes);
        }
        $xml = $this->buildXML(PhraseXML::TagPlural->value, element: implode("", $phrasesSubElems));
    
        return $xml;

    }
}
