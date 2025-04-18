<?php

namespace Schoolaid\Fel\Certification\Responses;

use Schoolaid\Fel\Certification\Contracts\StatusResponseInterface;

/**
 * Response for status check operations
 */
class StatusResponse extends BaseResponse implements StatusResponseInterface
{
    /**
     * Status of the document
     * 
     * @var string|null
     */
    protected ?string $status = null;
    
    /**
     * Original XML sent in the request
     * 
     * @var string|null
     */
    protected ?string $requestXml = null;
    
    /**
     * Constructor
     * 
     * @param bool $successful
     * @param string|null $status
     * @param array<int, string> $errors
     * @param mixed $rawResponse
     * @param string|null $requestXml
     */
    public function __construct(
        bool $successful = false,
        ?string $status = null,
        array $errors = [],
        mixed $rawResponse = null,
        ?string $requestXml = null
    ) {
        parent::__construct($successful, $errors, $rawResponse);
        
        $this->status = $status;
        $this->requestXml = $requestXml;
    }
    
    /**
     * Get the status of the document
     * 
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    /**
     * Get the original XML sent in the request
     * 
     * @return string|null
     */
    public function getRequestXml(): ?string
    {
        return $this->requestXml;
    }
    
    /**
     * Check if the document is certified
     * 
     * @return bool
     */
    public function isCertified(): bool
    {
        return $this->successful && ($this->status === 'CERTIFICADO' || $this->status === 'CERTIFIED');
    }
    
    /**
     * Check if the document is cancelled
     * 
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->successful && ($this->status === 'ANULADO' || $this->status === 'CANCELLED');
    }
} 