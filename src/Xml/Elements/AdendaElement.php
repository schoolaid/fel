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
    protected array $addendas = [];
    
    public function __construct(FelAddenda|array|null $addendas = null)
    {
        $this->builder = new XmlDocumentBuilder();
        
        if ($addendas instanceof FelAddenda) {
            $this->addendas = [$addendas];
        } elseif (is_array($addendas)) {
            $this->addendas = $addendas;
        }
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        if (empty($this->addendas)) {
            return '';
        }
        
        $children = [];
        
        foreach ($this->addendas as $addenda) {
            $children[$addenda->name] = $addenda->value;
        }
        
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
    
    /**
     * Check if the element has any addendas
     *
     * @return bool
     */
    public function hasAddendas(): bool
    {
        return !empty($this->addendas);
    }
} 