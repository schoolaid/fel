<?php

namespace Schoolaid\Fel\Certification\Actions;

/**
 * Action for cancelling documents - handles only HTTP communication
 */
class CancelAction extends BaseFelAction
{
    /**
     * Cancellation endpoint path
     * 
     * @var string
     */
    protected string $cancelEndpoint;
    
    /**
     * Constructor
     * 
     * @param string $baseUrl Base URL
     * @param string $cancelEndpoint Cancellation endpoint path
     * @param array<string, mixed> $clientConfig Additional HTTP client configuration
     */
    public function __construct(
        string $baseUrl, 
        string $cancelEndpoint = 'anulacion', 
        array $clientConfig = []
    ) {
        parent::__construct($baseUrl, $clientConfig);
        $this->cancelEndpoint = $cancelEndpoint;
    }
    
    /**
     * Get the URL for the cancellation endpoint
     * 
     * @return string
     */
    public function url(): string
    {
        // Return the endpoint path relative to the base URL
        return $this->cancelEndpoint;
    }
} 