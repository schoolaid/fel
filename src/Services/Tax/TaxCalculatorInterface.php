<?php

namespace Schoolaid\Fel\Services\Tax;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelTaxTotal;

/**
 * Interface for tax calculators
 */
interface TaxCalculatorInterface
{
    /**
     * Calculates the taxes for a specific item based on the document type and fiscal phrases
     *
     * @param FelItem $item Item to which taxes will be calculated
     * @param DocumentTypeEnum $documentType Document type
     * @param FelPhrases $phrases Fiscal phrases applicable
     * @return void
     */
    public function calculateItemTaxes(FelItem $item, DocumentTypeEnum $documentType, FelPhrases $phrases): void;
    
    /**
     * Calculates the taxes for a collection of items
     *
     * @param FelItems $items Collection of items
     * @param DocumentTypeEnum $documentType Document type
     * @param FelPhrases $phrases Fiscal phrases applicable
     * @return void
     */
    public function calculateItemsTaxes(FelItems $items, DocumentTypeEnum $documentType, FelPhrases $phrases): void;
    
    /**
     * Calculates the total of taxes for a collection of items
     *
     * @param FelItems $items Collection of items with taxes already calculated
     * @return FelTaxTotal
     */
    public function calculateTotalTaxes(FelItems $items): FelTaxTotal;
} 