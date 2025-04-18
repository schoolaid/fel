<?php

namespace Schoolaid\Fel\Certification\Contracts;

/**
 * Interface for provider credentials
 */
interface CredentialsInterface
{
    /**
     * Get the username for authentication
     *
     * @return string
     */
    public function getUsername(): string;
    
    /**
     * Get the API key or password for authentication
     *
     * @return string
     */
    public function getApiKey(): string;

    /**
     * Get the signature key for authentication
     *
     * @return string
     */
    public function getSignatureKey(): string;
    
    /**
     * Get additional parameters for authentication
     *
     * @return array<string, mixed>
     */
    public function getAdditionalParams(): array;
} 