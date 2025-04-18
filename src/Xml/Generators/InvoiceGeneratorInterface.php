<?php

namespace Schoolaid\Fel\Xml\Generators;

use Schoolaid\Fel\Models\Invoice;

interface InvoiceGeneratorInterface
{
    /**
     * Genera el documento XML para la factura
     *
     * @return string
     */
    public function generateXml(): string;
    
    /**
     * Obtiene el modelo de factura
     *
     * @return Invoice
     */
    public function getInvoice(): Invoice;
} 