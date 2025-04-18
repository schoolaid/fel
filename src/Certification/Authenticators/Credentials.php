<?php

namespace Schoolaid\Fel\Certification\Authenticators;

use Schoolaid\Fel\Certification\Contracts\CredentialsInterface;

/**
 * Basic implementation of the credentials interface
 */
class Credentials implements CredentialsInterface
{
    /**
     * Username for authentication
     * 
     * @var string
     */
    protected string $username;
    
    /**
     * API key for authentication
     * 
     * @var string
     */
    protected string $apiKey;
    
    /**
     * Signature key for authentication
     * 
     * @var string
     */
    protected string $signatureKey;
    
    /**
     * Additional parameters for authentication
     * 
     * @var array<string, mixed>
     */
    protected array $additionalParams = [];
    
    /**
     * Constructor
     * 
     * @param string $username
     * @param string $apiKey
     * @param string $signatureKey
     * @param array<string, mixed> $additionalParams
     */
    public function __construct(string $username, string $apiKey, string $signatureKey, array $additionalParams = [])
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->signatureKey = $signatureKey;
        $this->additionalParams = $additionalParams;
    }
    
    /**
     * Get the username for authentication
     * 
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    
    /**
     * Get the API key for authentication
     * 
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    
    /**
     * Get the signature key for authentication
     * 
     * @return string
     */
    public function getSignatureKey(): string
    {
        return $this->signatureKey;
    }
    
    /**
     * Get additional parameters for authentication
     * 
     * @return array<string, mixed>
     */
    public function getAdditionalParams(): array
    {
        return $this->additionalParams;
    }
} 