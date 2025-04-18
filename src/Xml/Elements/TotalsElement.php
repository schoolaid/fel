<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\TotalsXmlTags;

class TotalsElement implements XmlSerializable
{
    protected XmlDocumentBuilder $builder;
    protected FelTotals $totals;
    protected bool $useTaxes;
    
    public function __construct(FelTotals $totals, $useTaxes)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->totals = $totals;
        $this->useTaxes = $useTaxes;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $children = [];
        
        if($this->useTaxes) {
            if ($this->totals->taxTotal && !empty($this->totals->taxTotal->taxes)) {
                $totalTaxesChildren = $this->buildTotalTaxElements();
                $children[TotalsXmlTags::TotalTaxes->value] = $totalTaxesChildren;
            }
        }
        
        $children[TotalsXmlTags::GrandTotal->value] = $this->totals->grandTotal;
        
        return $this->builder->buildElement(
            $this->getXmlTagName(),
            [],
            $children
        );
    }
    
    /**
     * Builds the XML elements for the tax totals
     * 
     * @return array
     * @throws XmlGenerationException
     */
    protected function buildTotalTaxElements(): array
    {
        $taxChildren = [];
        
        $taxesByType = [];
        foreach ($this->totals->taxTotal->taxes as $tax) {
            $shortName = $tax->shortName->value;
            if (!isset($taxesByType[$shortName])) {
                $taxesByType[$shortName] = 0;
            }
            $taxesByType[$shortName] += $tax->taxAmount;
        }
        
        foreach ($taxesByType as $shortName => $amount) {
            $attributes = [
                TotalsXmlTags::ShortName->value => $shortName,
                TotalsXmlTags::TotalTaxAmount->value => $amount
            ];
            
            $taxChildren[] = $this->builder->buildElement(TotalsXmlTags::TotalTax->value, $attributes);
        }
        
        return $taxChildren;
    }
    
    public function getXmlTagName(): string
    {
        return TotalsXmlTags::Tag->value;
    }
} 