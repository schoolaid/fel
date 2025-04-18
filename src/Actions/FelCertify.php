<?php

namespace Schoolaid\Fel\Actions;

use Schoolaid\Fel\Certification\Contracts\CertificationResponseInterface;
use Schoolaid\Fel\Certification\Exceptions\AuthenticationException;
use Schoolaid\Fel\Certification\Exceptions\CertificationException;
use Schoolaid\Fel\Certification\FelCertificationService;
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Models\Invoice;
use Schoolaid\Fel\Traits\Makeable;

/**
 * Action for certifying FEL documents
 */
class FelCertify
{
    use Makeable;
    
    /**
     * Invoice to certify
     * 
     * @var Invoice
     */
    protected Invoice $invoice;
    
    /**
     * Configuration for FEL
     * 
     * @var FelConfig
     */
    protected FelConfig $config;
    
    /**
     * Constructor
     * 
     * @param Invoice $invoice
     * @param FelConfig $config
     */
    public function __construct(Invoice $invoice, FelConfig $config)
    {
        $this->invoice = $invoice;
        $this->config = $config;
    }

    /**
     * Execute the certification process
     *
     * @return CertificationResponseInterface
     * @throws AuthenticationException
     * @throws CertificationException
     */
    public function execute(): CertificationResponseInterface
    {
        // 1. Generate XML
        $xml = $this->getInvoiceXml();
        
        // 2. Create certification service
        $service = FelCertificationService::fromConfig($this->config);
        
        // 3. Certify the XML
        return $service->certify($xml);
    }
    
    /**
     * Get the XML for the invoice
     * 
     * @return string
     */
    protected function getInvoiceXml(): string
    {
        $generator = new FelGenerate($this->invoice);
        return $generator->generateXml();
    }
} 