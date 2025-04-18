<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\GeneralDataXmlTags;

class GeneralDataElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected string $emissionDateTime;
    protected string $currencyCode;
    protected string $documentType;
    
    public function __construct(string $emissionDateTime, string $currencyCode, string $documentType)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->emissionDateTime = $emissionDateTime;
        $this->currencyCode = $currencyCode;
        $this->documentType = $documentType;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $attributes = [
            GeneralDataXmlTags::EmissionDateTime->value => $this->emissionDateTime,
            GeneralDataXmlTags::CurrencyCode->value => $this->currencyCode,
            GeneralDataXmlTags::DocumentType->value => $this->documentType
        ];
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            $attributes
        );
    }
    
    public function getXmlTagName(): string
    {
        return GeneralDataXmlTags::Tag->value;
    }
} 