<?php

namespace Schoolaid\Fel\Actions;

use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Traits\Makeable;
use Schoolaid\Fel\Xml\Factory\DocumentGeneratorFactory;

class FelGenerate
{
    use Makeable;
    protected Invoice $invoice;
    
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
    
    /**
     * Generate the XML for the invoice.
     * 
     * @return string
     */
    public function generateXml(): string 
    {
        $generator = DocumentGeneratorFactory::createGenerator($this->invoice);
        
        return $generator->generateXml();
    }
}
