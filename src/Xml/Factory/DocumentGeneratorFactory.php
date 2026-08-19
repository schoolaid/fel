<?php

namespace Schoolaid\Fel\Xml\Factory;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Xml\Generators\CreditNoteGenerator;
use Schoolaid\Fel\Xml\Generators\DebitNoteGenerator;
use Schoolaid\Fel\Xml\Generators\DonationReceiptGenerator;
use Schoolaid\Fel\Xml\Generators\ExportInvoiceGenerator;
use Schoolaid\Fel\Xml\Generators\GeneralInvoiceGenerator;
use Schoolaid\Fel\Xml\Generators\InvoiceGeneratorInterface;

/**
 * Factory for creating the appropriate XML document generator
 */
class DocumentGeneratorFactory
{
    /**
     * Creates an appropriate XML document generator based on the invoice type
     *
     * @param Invoice $invoice
     * @return InvoiceGeneratorInterface
     */
    public static function createGenerator(Invoice $invoice): InvoiceGeneratorInterface
    {
        $documentType = DocumentTypeEnum::tryFrom($invoice->documentType) ?? DocumentTypeEnum::LOCAL_INVOICE;
        
        return match ($documentType) {
            DocumentTypeEnum::DONATION_RECEIPT => new DonationReceiptGenerator($invoice),
            DocumentTypeEnum::EXPORT_INVOICE => new ExportInvoiceGenerator($invoice),
            DocumentTypeEnum::CREDIT_NOTE => new CreditNoteGenerator($invoice),
            DocumentTypeEnum::DEBIT_NOTE => new DebitNoteGenerator($invoice),
            default => new GeneralInvoiceGenerator($invoice),
        };
    }
} 