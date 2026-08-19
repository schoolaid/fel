<?php

namespace Schoolaid\Fel\Certification;

use DateTimeInterface;
use Schoolaid\Fel\Certification\Contracts\CancellationResponseInterface;
use Schoolaid\Fel\Certification\Contracts\CertificationResponseInterface;
use Schoolaid\Fel\Certification\Contracts\ProviderInterface;
use Schoolaid\Fel\Certification\Contracts\StatusResponseInterface;
use Schoolaid\Fel\Certification\Exceptions\CertificationException;
use Schoolaid\Fel\Certification\Providers\InfileProvider;
use Schoolaid\Fel\Certification\Responses\CancellationResponse;
use Schoolaid\Fel\Config\FelConfig;

/**
 * Service for FEL certification operations
 */
class FelCertificationService
{
    /**
     * FEL Configuration
     * 
     * @var FelConfig
     */
    protected FelConfig $config;
    
    /**
     * Provider implementation
     * 
     * @var ProviderInterface
     */
    protected ProviderInterface $provider;
    
    /**
     * Constructor
     * 
     * @param FelConfig $config
     * @param ProviderInterface|null $provider
     */
    public function __construct(
        FelConfig $config,
        ?ProviderInterface $provider = null
    ) {
        $this->config = $config;
        $this->provider = $provider ?? $this->createDefaultProvider($config);
    }
    
    /**
     * Create the default provider based on configuration
     * 
     * @param FelConfig $config
     * @return ProviderInterface
     */
    protected function createDefaultProvider(FelConfig $config): ProviderInterface
    {
        $provider = strtolower($config->getProvider());
        
        // Create provider based on configured provider
        return match ($provider) {
            'infile' => new InfileProvider($config),
            default => throw new \InvalidArgumentException("Unsupported provider: {$provider}")
        };
    }

    /**
     * Create a new service instance from a configuration
     *
     * @param FelConfig $config
     * @return self
     */
    public static function fromConfig(FelConfig $config): self
    {
        return new self($config);
    }
    
    /**
     * Certify an XML document
     * 
     * @param string $xml
     * @return CertificationResponseInterface
     * @throws CertificationException
     */
    public function certify(string $xml): CertificationResponseInterface
    {
        try {
            return $this->provider->certify($xml);
        } catch (\Exception $e) {
            throw new CertificationException(
                'Certification failed: ' . $e->getMessage(),
                [],
                null,
                0,
                $e
            );
        }
    }

    /**
     * Check the status of a document
     *
     * @param string $uuid
     * @return StatusResponseInterface
     * @throws CertificationException
     */
    public function checkStatus(string $uuid): StatusResponseInterface
    {
        try {
            return $this->provider->checkStatus($uuid);
        } catch (\Exception $e) {
            throw new CertificationException(
                'Status check failed: ' . $e->getMessage(),
                [],
                null,
                0,
                $e
            );
        }
    }

    /**
     * Cancel a document using a Cancellation model
     *
     * @throws CertificationException
     */
    public function cancel(string $xml): CancellationResponse
    {
        try {
            return $this->provider->cancel($xml);
        } catch (\Exception $e) {
            throw new CertificationException(
                'Cancellation failed: ' . $e->getMessage(),
                [],
                null,
                0,
                $e
            );
        }
    }
    
    /**
     * Get the configuration used by this service
     * 
     * @return FelConfig
     */
    public function getConfig(): FelConfig
    {
        return $this->config;
    }
    
    /**
     * Get the provider used by this service
     * 
     * @return ProviderInterface
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }
} 