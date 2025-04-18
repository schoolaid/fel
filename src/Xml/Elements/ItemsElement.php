<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\ItemsXmlTags;
use Schoolaid\Fel\Xml\Enums\TaxXmlTags;

class ItemsElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected FelItems $items;
    
    public function __construct(FelItems $items)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->items = $items;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $children = [];
        
        foreach ($this->items->items as $item) {
            $itemAttributes = [
                ItemsXmlTags::LineNumber->value => $item->lineNumber,
                ItemsXmlTags::GoodOrService->value => $item->goodOrService
            ];
            
            $itemChildren = [
                ItemsXmlTags::Quantity->value => $item->quantity,
                ItemsXmlTags::UnitMeasure->value => $item->unitMeasure,
                ItemsXmlTags::Description->value => $item->description,
                ItemsXmlTags::UnitPrice->value => $item->unitPrice,
                ItemsXmlTags::Price->value => $item->price,
                ItemsXmlTags::Discount->value => $item->discount
            ];
            
            if (!empty($item->taxes)) {
                $taxesXml = $this->buildTaxesElements($item->taxes);
                $itemChildren[ItemsXmlTags::Taxes->value] = $taxesXml;
            }
            
            $itemChildren[ItemsXmlTags::Total->value] = $item->total;
            
            $children[] = $this->builder->buildElement(ItemsXmlTags::Item->value, $itemAttributes, $itemChildren);
        }
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            [],
            $children
        );
    }
    
    /**
     * Builds the XML elements for the taxes
     * 
     * @param array $taxes
     * @return array
     * @throws XmlGenerationException
     */
    protected function buildTaxesElements(array $taxes): array
    {
        $taxChildren = [];
        
        foreach ($taxes as $tax) {
            $taxData = [
                TaxXmlTags::ShortName->value => $tax->shortName->value,
                TaxXmlTags::TaxableUnitCode->value => $tax->taxableUnitCode,
                TaxXmlTags::TaxableAmount->value => $tax->taxableAmount,
                TaxXmlTags::TaxAmount->value => $tax->taxAmount
            ];
            
            $taxChildren[] = $this->builder->buildElement(ItemsXmlTags::Tax->value, [], $taxData);
        }
        
        return $taxChildren;
    }
    
    public function getXmlTagName(): string
    {
        return ItemsXmlTags::Tag->value;
    }
} 