<?php

namespace Schoolaid\Fel\Services\Tax;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelTaxTotal;

/**
 * Calculator for donation receipts (no taxes)
 */
class DonationTaxCalculator implements TaxCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculateItemTaxes(FelItem $item, DocumentTypeEnum $documentType, FelPhrases $phrases): void
    {
        // For donation receipts, we don't apply any taxes
        $item->taxes = [];
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
        // For donation receipts, there are no taxes
        return new FelTaxTotal([]);
    }
} 