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
     * UUID of the document to check
     *
     * @var string|null
     */
    protected ?string $uuid = null;

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
     * Set the UUID of the document to check
     *
     * @param string $uuid
     * @return self
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Get the URL for the status endpoint, including the UUID when set
     *
     * @return string
     */
    public function url(): string
    {
        if ($this->uuid !== null) {
            return $this->statusEndpoint . '?uuid=' . urlencode($this->uuid);
        }

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