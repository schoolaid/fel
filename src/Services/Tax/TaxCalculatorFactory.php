<?php

namespace Schoolaid\Fel\Services\Tax;

use Schoolaid\Fel\Enums\DocumentTypeEnum;

/**
 * Factory for tax calculators
 */
class TaxCalculatorFactory
{
    /**
     * Creates the appropriate tax calculator based on the document type
     *
     * @param DocumentTypeEnum $documentType
     * @return TaxCalculatorInterface
     */
    public static function create(DocumentTypeEnum $documentType): TaxCalculatorInterface
    {
        return match($documentType) {
            // Specific calculators for each document type
            DocumentTypeEnum::DONATION_RECEIPT => new DonationTaxCalculator(),
            DocumentTypeEnum::EXPORT_INVOICE => new ExportTaxCalculator(),
            
            // By default, we use the general calculator
            default => new GeneralTaxCalculator(),
        };
    }
} 