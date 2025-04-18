<?php

namespace Schoolaid\Fel\Certification\Contracts;

use DateTimeInterface;

/**
 * Interface for cancellation responses
 */
interface CancellationResponseInterface
{
    /**
     * Check if the cancellation was successful
     *
     * @return bool
     */
    public function isSuccessful(): bool;
    
    /**
     * Get the cancellation date
     *
     * @return DateTimeInterface|null
     */
    public function getCancellationDate(): ?DateTimeInterface;
    
    /**
     * Get any errors that occurred during cancellation
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