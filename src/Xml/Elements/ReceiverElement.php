<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\ReceiverXmlTags;

class ReceiverElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected FelReceiver $receiver;
    protected AddressElement $address;
    
    public function __construct(FelReceiver $receiver)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->receiver = $receiver;
        $this->address = new AddressElement(
            $receiver->address,
            'dte:DireccionReceptor'
        );
    }
    
    public function asXML(): string
    {
        $attributes = [
            ReceiverXmlTags::TaxId->value => $this->receiver->id,
            ReceiverXmlTags::CustomerName->value => $this->receiver->name,
        ];
        
        if (!empty($this->receiver->email)) {
            $attributes[ReceiverXmlTags::EmailCustomer->value] = $this->receiver->email;
        }
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            $attributes,
            $this->address->asXML()
        );
    }
    
    public function getXmlTagName(): string
    {
        return ReceiverXmlTags::Tag->value;
    }
} 