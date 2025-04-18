<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelAddenda;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\AdendaXmlTags;

class AdendaElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected ?FelAddenda $addenda;
    
    public function __construct(?FelAddenda $addenda = null)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->addenda = $addenda;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        if (!$this->addenda) {
            return '';
        }
        
        $children = [
            $this->addenda->name => $this->addenda->value
        ];
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            [],
            $children
        );
    }
    
    public function getXmlTagName(): string
    {
        return AdendaXmlTags::Tag->value;
    }
} 