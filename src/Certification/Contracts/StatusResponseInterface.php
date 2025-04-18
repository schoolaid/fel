<?php

namespace Schoolaid\Fel\Certification\Contracts;

/**
 * Interface for status check responses
 */
interface StatusResponseInterface
{
    /**
     * Check if the status check was successful
     *
     * @return bool
     */
    public function isSuccessful(): bool;
    
    /**
     * Get the status of the document
     *
     * @return string|null
     */
    public function getStatus(): ?string;
    
    /**
     * Check if the document is certified
     *
     * @return bool
     */
    public function isCertified(): bool;
    
    /**
     * Check if the document is cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool;
    
    /**
     * Get any errors that occurred during the status check
     *
     * @return array<int, string>
     */
    public function getErrors(): array;
    
    /**
     * Get the raw response from the provider
     *
     * @return mixed
     */
    public function getRawResponse(): mixed;
    
    /**
     * Get the original XML sent in the request
     *
     * @return string|null
     */
    public function getRequestXml(): ?string;
} 