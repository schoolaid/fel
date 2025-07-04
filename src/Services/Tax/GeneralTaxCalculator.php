<?php

namespace Schoolaid\Fel\Services\Tax;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\TaxEnum;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelTax;
use Schoolaid\Fel\Models\FelTaxTotal;

/**
 * Calculator for general tax regime
 */
class GeneralTaxCalculator implements TaxCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculateItemTaxes(FelItem $item, DocumentTypeEnum $documentType, FelPhrases $phrases): void
    {
        // Clear existing taxes
        $item->taxes = [];
        
        // Specific logic according to the document type and phrases
        switch ($documentType) {
            case DocumentTypeEnum::LOCAL_INVOICE:
                // For normal invoices with IVA
                $this->calculateIVA($item);
                break;

            case DocumentTypeEnum::SMALL_TAXPAYER_INVOICE:
            case DocumentTypeEnum::EXPORT_INVOICE:
                // For export invoices - no IVA
                break;
                
            case DocumentTypeEnum::SPECIAL_INVOICE:
                // For special invoices
                $this->calculateIVA($item);
                break;

            default:
                // By default, apply IVA
                $this->calculateIVA($item);
                break;
        }
        
        // Recalculate the total of the item
        $item->total = $item->calculateTotal();
    }
    
    /**
     * {@inheritdoc}
     */
    public function calculateItemsTaxes(FelItems $items, DocumentTypeEnum $documentType, FelPhrases $phrases): void
    {
        foreach ($items->items as $item) {
            $this->calculateItemTaxes($item, $documentType, $phrases);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function calculateTotalTaxes(FelItems $items): FelTaxTotal
    {
        // Collect all taxes from the items
        $allTaxes = [];
        foreach ($items->items as $item) {
            $allTaxes = array_merge($allTaxes, $item->taxes);
        }
        
        // Create and calculate the total of taxes
        $taxTotal = new FelTaxTotal($allTaxes);
        $taxTotal->calculate();
        
        return $taxTotal;
    }
    
    /**
     * Calculates the IVA for an item
     * 
     * @param FelItem $item
     * @return void
     */
    protected function calculateIVA(FelItem $item): void
    {
        $tax = new FelTax(TaxEnum::IVA, $item->total);
        $tax->calculate();
        $item->taxes[] = $tax;
    }
} 
