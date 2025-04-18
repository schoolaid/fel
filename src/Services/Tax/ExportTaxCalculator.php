<?php

namespace Schoolaid\Fel\Services\Tax;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelTaxTotal;

/**
 * Calculator for export invoices (no taxes)
 */
class ExportTaxCalculator implements TaxCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculateItemTaxes(FelItem $item, DocumentTypeEnum $documentType, FelPhrases $phrases): void
    {
        // For export invoices, we don't apply IVA
        $item->taxes = [];
        
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
        // For export invoices, there are no taxes
        return new FelTaxTotal([]);
    }
} 