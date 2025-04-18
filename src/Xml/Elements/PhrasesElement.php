<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\PhrasesXmlTags;

class PhrasesElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected FelPhrases $phrases;
    
    public function __construct(FelPhrases $phrases)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->phrases = $phrases;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $children = [];
        
        if (!empty($this->phrases->phrases)) {
            foreach ($this->phrases->phrases as $phrase) {
                $attributes = [
                    PhrasesXmlTags::ScenarioCode->value => $phrase->scenarioCode,
                    PhrasesXmlTags::PhraseType->value => $phrase->phraseType
                ];
                
                $children[] = $this->builder->buildElement(PhrasesXmlTags::Phrase->value, $attributes);
            }
        } else {
            $attributes = [
                PhrasesXmlTags::ScenarioCode->value => '1',
                PhrasesXmlTags::PhraseType->value => '1'
            ];
            
            $children[] = $this->builder->buildElement(PhrasesXmlTags::Phrase->value, $attributes);
        }
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            [],
            $children
        );
    }
    
    public function getXmlTagName(): string
    {
        return PhrasesXmlTags::Tag->value;
    }
} 