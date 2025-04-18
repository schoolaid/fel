<?php

namespace Schoolaid\Fel\Actions;

use DOMException;
use Schoolaid\Fel\Certification\Exceptions\CertificationException;
use Schoolaid\Fel\Certification\FelCertificationService;
use Schoolaid\Fel\Certification\Responses\CancellationResponse;
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Models\Cancellation;
use Schoolaid\Fel\Traits\Makeable;
use Schoolaid\Fel\Xml\Generators\CancellationGenerator;

/**
 * Action for cancelling FEL documents
 */
class FelCancel
{
    use Makeable;
    
    /**
     * Cancellation model containing all required data
     * 
     * @var Cancellation
     */
    protected Cancellation $cancellation;
    
    /**
     * Configuration for FEL
     * 
     * @var FelConfig
     */
    protected FelConfig $config;
    
    /**
     * Constructor
     * 
     * @param Cancellation $cancellation Cancellation model with all data
     * @param FelConfig $config FEL configuration
     */
    public function __construct(Cancellation $cancellation, FelConfig $config)
    {
        $this->cancellation = $cancellation;
        $this->config = $config;
    }

    /**
     * Static constructor from parameters
     *
     * @param string $uuid Document UUID to cancel
     * @param string $issuerNit Issuer's NIT (tax ID)
     * @param string $reason Reason for cancellation
     * @param FelConfig $config FEL configuration
     * @param string $idReceiver Receiver's ID (defaults to 'CF' for final consumer)
     * @param string|null $documentDateTime Document's issue date (defaults to current date)
     * @param string|null $cancellationDateTime Cancellation date and time (defaults to current date)
     * @return static
     */
    public static function fromParams(
        string             $uuid,
        string             $issuerNit,
        string             $reason,
        FelConfig          $config,
        string             $idReceiver = 'CF',
        ?string $documentDateTime = null,
        ?string $cancellationDateTime = null
    ): self {
        $cancellation = new Cancellation(
            $uuid,
            $issuerNit,
            $idReceiver,
            $reason,
            $documentDateTime ?? now()->format('Y-m-d\TH:i:s'),
            $cancellationDateTime
        );
        
        return new self($cancellation, $config);
    }

    /**
     * Generate XML for cancellation
     *
     * @return string
     * @throws DOMException
     */
    public function generateXml(): string
    {
        // Generate cancellation XML
        $generator = new CancellationGenerator();
        return $generator->generate($this->cancellation);
    }

    /**
     * Execute the cancellation process
     *
     * @return CancellationResponse
     * @throws CertificationException|DOMException
     */
    public function execute(): CancellationResponse
    {
        $xml = $this->generateXml();
        // Create certification service
        $service = FelCertificationService::fromConfig($this->config);
        
        // Cancel the document with model data
        return $service->cancel($xml);
    }
    
    /**
     * Get the cancellation model
     *
     * @return Cancellation
     */
    public function getCancellation(): Cancellation
    {
        return $this->cancellation;
    }
}
