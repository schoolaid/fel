<?php

namespace Schoolaid\Fel\Xml\Generators;

use Schoolaid\Fel\Xml\Elements\AdendaElement;
use Schoolaid\Fel\Xml\Elements\DTEElement;
use Schoolaid\Fel\Xml\Elements\EmissionDataElement;
use Schoolaid\Fel\Xml\Elements\GTDocument;
use Schoolaid\Fel\Xml\Elements\SATElement;

/**
 * Generator for donation receipts
 */
class DonationReceiptGenerator extends AbstractInvoiceGenerator
{
    public function generateXml(): string
    {
        $this->invoice->setUseTaxes(true);
        return parent::generateXml();
     }
} 