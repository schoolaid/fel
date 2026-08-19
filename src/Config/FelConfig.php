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
        $config = config('fel');
        $this->provider = $provider ?: $config['provider'] ?? '';
        $this->username = $username ?: $config['username'] ?? '';
        $this->apiKey = $apiKey ?: $config['api_key'] ?? '';
        $this->signatureKey = $signatureKey ?: $config['signature_key'] ?? '';

        if (empty($providerConfig)) {
            $providerConfig = $config['provider_config'] ?? [
                'base_url' => $config['provider_config']['base_url'] ?? '',
                'certify_url' => $config['provider_config']['certify_url'] ?? '',
                'status_url' => $config['provider_config']['status_url'] ?? '',
                'cancel_url' => $config['provider_config']['cancel_url'] ?? '',
                'timeout' => $config['provider_config']['timeout'] ?? 30,
                'verify_ssl' => $config['provider_config']['verify_ssl'] ?? true,
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
     * Create a new instance from a configuration file
     *
     * @return self
     */
    public static function fromConfig(): self
    {
        return new self();
    }

    /**
     * Crea la configuración para INFILE con los nombres veraces de las llaves.
     *
     * A diferencia del constructor histórico (cuyos parámetros apiKey /
     * signatureKey están invertidos y no pueden renombrarse sin afectar a las
     * apps que ya lo usan), aquí cada llave se llama como el header al que va:
     *
     *   $llaveFirma -> header llaveFirma      (llave del firmador)
     *   $llaveApi   -> headers llaveApi/llave (llave del API REST)
     *
     * Preferir este constructor en código nuevo.
     *
     * @param string $username
     * @param string $llaveFirma Llave del firmador
     * @param string $llaveApi Llave del API REST
     * @param array<string, mixed> $providerConfig
     * @return self
     */
    public static function forInfile(
        string $username,
        string $llaveFirma,
        string $llaveApi,
        array $providerConfig = []
    ): self {
        return new self('infile', $username, $llaveFirma, $llaveApi, $providerConfig);
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
     * ATENCIÓN: nombre histórico invertido — este slot contiene la LLAVE DEL
     * FIRMADOR (viaja como llaveFirma). En código nuevo usa getLlaveFirma().
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
     * ATENCIÓN: nombre histórico invertido — este slot contiene la LLAVE DEL
     * API REST (viaja como llaveApi/llave). En código nuevo usa getLlaveApi().
     *
     * @return string
     */
    public function getSignatureKey(): string
    {
        return $this->signatureKey;
    }

    /**
     * Llave del firmador de INFILE (se envía como header llaveFirma).
     *
     * Accesor veraz del slot histórico apiKey: por compatibilidad con los
     * consumidores existentes, la llave de firma vive en $apiKey y no se
     * renombra el parámetro del constructor.
     *
     * @return string
     */
    public function getLlaveFirma(): string
    {
        return $this->apiKey;
    }

    /**
     * Set the signer key (llave del firmador)
     *
     * @param string $llaveFirma
     * @return self
     */
    public function setLlaveFirma(string $llaveFirma): self
    {
        $this->apiKey = $llaveFirma;
        return $this;
    }

    /**
     * Llave del API REST de INFILE (se envía como headers llaveApi y llave).
     *
     * Accesor veraz del slot histórico signatureKey.
     *
     * @return string
     */
    public function getLlaveApi(): string
    {
        return $this->signatureKey;
    }

    /**
     * Set the REST API key (llave del API)
     *
     * @param string $llaveApi
     * @return self
     */
    public function setLlaveApi(string $llaveApi): self
    {
        $this->signatureKey = $llaveApi;
        return $this;
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