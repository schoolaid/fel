<?php

namespace Schoolaid\Fel\Xml\Generators;

use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Services\Tax\TaxCalculatorFactory;
use Schoolaid\Fel\Xml\Elements\AdendaElement;
use Schoolaid\Fel\Xml\Elements\DTEElement;
use Schoolaid\Fel\Xml\Elements\EmissionDataElement;
use Schoolaid\Fel\Xml\Elements\GTDocument;
use Schoolaid\Fel\Xml\Elements\SATElement;
use Schoolaid\Fel\Enums\DocumentTypeEnum;

/**
 * Base abstract class for all invoice generators
 */
abstract class AbstractInvoiceGenerator implements InvoiceGeneratorInterface
{
    /**
     * The invoice to generate XML for
     */
    protected Invoice $invoice;
    
    /**
     * Constructor
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
    
    /**
     * {@inheritdoc}
     * @throws XmlGenerationException
     */
    public function generateXml(): string
    {
        // Calculate taxes according to the document type
        $this->calculateTaxes();
        
        // Build emission data
        $emissionData = new EmissionDataElement($this->invoice);
        
        // Build DTE element
        $dte = new DTEElement($emissionData);
        
        // Build adenda if needed
        $adenda = null;
        if ($this->invoice->addendas) {
            $adenda = new AdendaElement($this->invoice->addendas);
        }
        
        // Build SAT element
        $sat = new SATElement($dte, $adenda);
        
        // Build the main document
        $document = new GTDocument($sat);
        
        // Return the XML
        return $document->asXML();
    }
    
    /**
     * Calculate taxes according to the document type
     */
    protected function calculateTaxes(): void
    {
        // Get appropriate tax calculator
        $documentType = $this->invoice->documentType;
        
        // Convert to enum if it's a string
        if (is_string($documentType)) {
            $documentType = DocumentTypeEnum::tryFrom($documentType) ?? DocumentTypeEnum::LOCAL_INVOICE;
        }
        
        $taxCalculator = TaxCalculatorFactory::create($documentType);
        
        // Calculate taxes for all items
        $taxCalculator->calculateItemsTaxes(
            $this->invoice->items, 
            $documentType, 
            $this->invoice->phrases
        );
        
        // Calculate total taxes
        $taxTotal = $taxCalculator->calculateTotalTaxes($this->invoice->items);
        
        // Update invoice totals
        $this->invoice->totals->taxTotal = $taxTotal;
        $this->invoice->totals->grandTotal = $this->invoice->items->calculateTotal();
    }
} 