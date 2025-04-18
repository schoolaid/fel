<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\IssuerXmlTags;

class IssuerElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected FelIssuer $issuer;
    protected AddressElement $address;
    
    public function __construct(FelIssuer $issuer)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->issuer = $issuer;
        $this->address = new AddressElement(
            $issuer->address,
            IssuerXmlTags::Address->value
        );
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $attributes = [
            IssuerXmlTags::Email->value => $this->issuer->email,
            IssuerXmlTags::EstablishmentCode->value => $this->issuer->establishmentCode,
            IssuerXmlTags::TaxId->value => $this->issuer->nit,
            IssuerXmlTags::CommercialName->value => $this->issuer->commercialName,
            IssuerXmlTags::VatAffiliation->value => $this->issuer->vatAffiliation->value,
            IssuerXmlTags::Name->value => $this->issuer->name
        ];
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            $attributes,
            $this->address->asXML()
        );
    }
    
    public function getXmlTagName(): string
    {
        return IssuerXmlTags::Tag->value;
    }
} 