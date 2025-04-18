<?php

namespace Schoolaid\Fel\Certification\Actions;

/**
 * Action for checking document status - handles only HTTP communication
 */
class StatusAction extends BaseFelAction
{
    /**
     * Status endpoint path
     * 
     * @var string
     */
    protected string $statusEndpoint;
    
    /**
     * Constructor
     * 
     * @param string $baseUrl Base URL
     * @param string $statusEndpoint Status endpoint path
     * @param array<string, mixed> $clientConfig Additional HTTP client configuration
     */
    public function __construct(
        string $baseUrl, 
        string $statusEndpoint = 'consultarEstatus', 
        array $clientConfig = []
    ) {
        parent::__construct($baseUrl, $clientConfig);
        $this->statusEndpoint = $statusEndpoint;
        $this->method = 'GET';
    }
    
    /**
     * Get the URL for the status endpoint
     * 
     * @return string
     */
    public function url(): string
    {
        // Return the endpoint path relative to the base URL
        return $this->statusEndpoint;
    }
    
    /**
     * Override method to use GET for status checks
     * 
     * @return string
     */
    public function method(): string
    {
        return 'GET';
    }
} 