<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\DocumentXmlTags;

class DTEElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected EmissionDataElement $emissionData;
    
    public function __construct(EmissionDataElement $emissionData)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->emissionData = $emissionData;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $attributes = [
            DocumentXmlTags::ID->value => DocumentXmlTags::CertifiedData->value
        ];
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            $attributes,
            $this->emissionData->asXML()
        );
    }
    
    public function getXmlTagName(): string
    {
        return DocumentXmlTags::DTE->value;
    }
} 