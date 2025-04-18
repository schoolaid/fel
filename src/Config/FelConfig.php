<?php

namespace Schoolaid\Fel\Config;

/**
 * Configuration for FEL operations
 */
class FelConfig
{
    /**
     * Provider to use
     * 
     * @var string
     */
    protected string $provider;
    
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
     * Signature Key for Infile
     * 
     * @var string
     */
    protected string $signatureKey;
    
    /**
     * Specific configuration for the provider
     * 
     * @var array<string, mixed>
     */
    protected array $providerConfig = [];
    
    /**
     * Constructor
     * 
     * @param string $provider
     * @param string $username
     * @param string $apiKey
     * @param string $signatureKey
     * @param array<string, mixed> $providerConfig
     */
    public function __construct(
        string $provider = '',
        string $username = '',
        string $apiKey = '',
        string $signatureKey = '',
        array $providerConfig = []
    ) {
        $this->provider = $provider ?: $_ENV['FEL_PROVIDER'] ?? '';
        $this->username = $username ?: $_ENV['FEL_USERNAME'] ?? '';
        $this->apiKey = $apiKey ?: $_ENV['FEL_KEY'] ?? '';
        $this->signatureKey = $signatureKey ?: $_ENV['FEL_PASSWORD'] ?? '';

        if (empty($providerConfig)) {
            $providerConfig = [
                'base_url' => $_ENV['FEL_BASE_URL'] ?? '',
                'certify_url' => $_ENV['FEL_CERTIFY_URL'] ?? '',
                'status_url' => $_ENV['FEL_STATUS_URL'] ?? '',
                'cancel_url' => $_ENV['FEL_CANCEL_URL'] ?? '',
                'timeout' => $_ENV['FEL_TIMEOUT'] ?? 30,
                'verify_ssl' => $_ENV['FEL_VERIFY_SSL'] ?? true,
            ];
        }

        $this->providerConfig = $providerConfig;
    }
    
    /**
     * Create a new config instance from an array
     * 
     * @param array<string, mixed> $config
     * @return self
     */
    public static function fromArray(array $config): self
    {
        return new self(
            $config['provider'] ?? '',
            $config['username'] ?? '',
            $config['api_key'] ?? '',
            $config['signature_key'] ?? $config['llave_firma'] ?? '',
            $config['provider_config'] ?? []
        );
    }
    
    /**
     * Create a new instance from environment variables
     * 
     * @return self
     */
    public static function fromEnv(): self
    {
        return new self();
    }
    
    /**
     * Get the provider
     * 
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
    
    /**
     * Get the username
     * 
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    
    /**
     * Get the API key
     * 
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    
    /**
     * Get the signature key
     * 
     * @return string
     */
    public function getSignatureKey(): string
    {
        return $this->signatureKey;
    }
    
    /**
     * Get the provider config
     * 
     * @return array<string, mixed>
     */
    public function getProviderConfig(): array
    {
        return $this->providerConfig;
    }
    
    /**
     * Set the provider
     * 
     * @param string $provider
     * @return self
     */
    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
    
    /**
     * Set the username
     * 
     * @param string $username
     * @return self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    
    /**
     * Set the API key
     * 
     * @param string $apiKey
     * @return self
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }
    
    /**
     * Set the signature key
     * 
     * @param string $signatureKey
     * @return self
     */
    public function setSignatureKey(string $signatureKey): self
    {
        $this->signatureKey = $signatureKey;
        return $this;
    }
    
    /**
     * Set the provider config
     * 
     * Configure provider-specific settings. For Infile provider, you can include:
     * - base_url: API base URL
     * - timeout: Request timeout in seconds
     * - verify_ssl: Whether to verify SSL certificates
     * - identifier: Tax ID or identifier for the issuer
     * 
     * @param array<string, mixed> $providerConfig
     * @return self
     */
    public function setProviderConfig(array $providerConfig): self
    {
        $this->providerConfig = $providerConfig;
        return $this;
    }
    
    /**
     * Set the identifier value in provider config
     * 
     * @param string $identifier
     * @return self
     */
    public function setIdentifier(string $identifier): self
    {
        $this->providerConfig['identifier'] = $identifier;
        return $this;
    }
    
    /**
     * Get the identifier value from provider config
     * 
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->providerConfig['identifier'] ?? '';
    }
} 