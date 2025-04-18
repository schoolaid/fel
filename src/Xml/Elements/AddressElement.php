<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\AddressXmlTags;

class AddressElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected FelAddress $address;
    protected string $tagName;
    
    public function __construct(FelAddress $address, string $tagName)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->address = $address;
        $this->tagName = $tagName;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $children = [
            AddressXmlTags::Address->value => $this->address->street,
            AddressXmlTags::PostalCode->value => $this->address->postalCode,
            AddressXmlTags::Municipality->value => $this->address->municipality,
            AddressXmlTags::Department->value => $this->address->department,
            AddressXmlTags::Country->value => $this->address->country
        ];
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            [],
            $children
        );
    }
    
    public function getXmlTagName(): string
    {
        return $this->tagName;
    }
} 