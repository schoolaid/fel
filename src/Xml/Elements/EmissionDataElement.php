<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\DocumentXmlTags;

class EmissionDataElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected GeneralDataElement $generalData;
    protected IssuerElement $issuer;
    protected ReceiverElement $receiver;
    protected PhrasesElement $phrases;
    protected ItemsElement $items;
    protected TotalsElement $totals;
    protected ?ComplementsElement $complements = null;

    public function __construct(Invoice $invoice)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->generalData = new GeneralDataElement(
            $invoice->emissionDateTime, 
            $invoice->currencyCode, 
            $invoice->documentType,
            $invoice->personType
        );
        $this->issuer = new IssuerElement($invoice->issuer);
        $this->receiver = new ReceiverElement($invoice->receiver);
        $this->phrases = new PhrasesElement($invoice->phrases);
        $this->items = new ItemsElement($invoice->items);
        $this->totals = new TotalsElement($invoice->totals, $invoice->useTaxes);

        if ($invoice->hasReferenceNote()) {
            $this->complements = new ComplementsElement(
                $invoice->getReferenceNote(),
                $invoice->documentType
            );
        }
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $attributes = [
            DocumentXmlTags::ID->value => DocumentXmlTags::EmissionDataID->value
        ];
        
        $children = [
            $this->generalData->asXML(),
            $this->issuer->asXML(),
            $this->receiver->asXML(),
            $this->phrases->asXML(),
            $this->items->asXML(),
            $this->totals->asXML()
        ];

        if ($this->complements !== null) {
            $children[] = $this->complements->asXML();
        }

        return $this->builder->buildElement(
            $this->getXmlTagName(),
            $attributes,
            $children
        );
    }
    
    public function getXmlTagName(): string
    {
        return DocumentXmlTags::EmissionData->value;
    }
} 