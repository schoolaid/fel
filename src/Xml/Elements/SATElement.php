<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\DocumentXmlTags;

class SATElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected DTEElement $dte;
    protected ?AdendaElement $adenda;
    
    public function __construct(DTEElement $dte, ?AdendaElement $adenda = null)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->dte = $dte;
        $this->adenda = $adenda;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $attributes = [
            DocumentXmlTags::DocumentClass->value => 'dte'
        ];
        
        $children = [$this->dte->asXML()];
        
        if ($this->adenda && $this->adenda->hasAddendas()) {
            $children[] = $this->adenda->asXML();
        }
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            $attributes,
            $children
        );
    }
    
    public function getXmlTagName(): string
    {
        return DocumentXmlTags::SAT->value;
    }
} 