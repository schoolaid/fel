<?php

namespace Tests\Unit;

use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Xml\Factory\DocumentGeneratorFactory;
use Schoolaid\Fel\Xml\Generators\DonationReceiptGenerator;
use Schoolaid\Fel\Xml\Generators\ExportInvoiceGenerator;
use Schoolaid\Fel\Xml\Generators\GeneralInvoiceGenerator;

it('returns correct generator type based on document type', function () {
    // Create invoices with different document types
    $generalInvoice = new Invoice(DocumentTypeEnum::LOCAL_INVOICE);
    $donationReceipt = new Invoice(DocumentTypeEnum::DONATION_RECEIPT);
    $exportInvoice = new Invoice(DocumentTypeEnum::EXPORT_INVOICE);
    
    // Get generators
    $generalGenerator = DocumentGeneratorFactory::createGenerator($generalInvoice);
    $donationGenerator = DocumentGeneratorFactory::createGenerator($donationReceipt);
    $exportGenerator = DocumentGeneratorFactory::createGenerator($exportInvoice);
    
    // Verify correct types
    expect($generalGenerator)->toBeInstanceOf(GeneralInvoiceGenerator::class)
        ->and($donationGenerator)->toBeInstanceOf(DonationReceiptGenerator::class)
        ->and($exportGenerator)->toBeInstanceOf(ExportInvoiceGenerator::class);
});

it('generates correct XML for different document types', function () {
    // Create a basic invoice for each type
    $generalInvoice = createBasicInvoice(DocumentTypeEnum::LOCAL_INVOICE);
    $donationInvoice = createBasicInvoice(DocumentTypeEnum::DONATION_RECEIPT);
    $exportInvoice = createBasicInvoice(DocumentTypeEnum::EXPORT_INVOICE);
    
    // Generate XML for each
    $generalXml = DocumentGeneratorFactory::createGenerator($generalInvoice)->generateXml();
    $donationXml = DocumentGeneratorFactory::createGenerator($donationInvoice)->generateXml();
    $exportXml = DocumentGeneratorFactory::createGenerator($exportInvoice)->generateXml();
    
    // Verify all XMLs are valid
    expect($generalXml)->toBeString()->toContain('<dte:GTDocumento')
        ->and($donationXml)->toBeString()->toContain('<dte:GTDocumento')
        ->and($exportXml)->toBeString()->toContain('<dte:GTDocumento')
        ->and($generalXml)->toContain('Tipo="' . DocumentTypeEnum::LOCAL_INVOICE->value . '"')
        ->and($donationXml)->toContain('Tipo="' . DocumentTypeEnum::DONATION_RECEIPT->value . '"')
        ->and($exportXml)->toContain('Tipo="' . DocumentTypeEnum::EXPORT_INVOICE->value . '"')
        ->and($generalXml)->toContain('<dte:NombreCorto>IVA</dte:NombreCorto>')
        ->and($donationXml)->not->toContain('<dte:NombreCorto>IVA</dte:NombreCorto>')
        ->and($exportXml)->not->toContain('<dte:NombreCorto>IVA</dte:NombreCorto>');

    // Verify document types

    // Verify taxes (only general invoices should have IVA)
});

// Helper function to create a basic invoice
function createBasicInvoice(DocumentTypeEnum $documentType): Invoice {
    return new Invoice(
        $documentType,
        date('Y-m-d\TH:i:sP'),
        CurrencyEnum::QUETZAL,
        null,
        null,
        new FelPhrases(),
        new FelItems([
            [
                'lineNumber' => 1,
                'description' => 'Test Item',
                'unitPrice' => 100,
                'price' => 100,
                'total' => 100
            ]
        ]),
        new FelTotals()
    );
} 