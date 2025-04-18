<?php

namespace Schoolaid\Fel\Certification\Actions;

/**
 * Action for certifying documents - handles only HTTP communication
 */
class CertifyAction extends BaseFelAction
{
    /**
     * Certification endpoint path
     * 
     * @var string
     */
    protected string $certifyEndpoint;
    
    /**
     * Constructor
     * 
     * @param string $baseUrl Base URL
     * @param string $certifyEndpoint Certification endpoint path
     * @param array<string, mixed> $clientConfig Additional HTTP client configuration
     */
    public function __construct(
        string $baseUrl, 
        string $certifyEndpoint = 'certificacion', 
        array $clientConfig = []
    ) {
        parent::__construct($baseUrl, $clientConfig);
        $this->certifyEndpoint = $certifyEndpoint;
    }
    
    /**
     * Get the URL for the certification endpoint
     * 
     * @return string
     */
    public function url(): string
    {
        // Return the endpoint path relative to the base URL
        return $this->certifyEndpoint;
    }
} 