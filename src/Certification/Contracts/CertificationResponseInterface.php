<?php

namespace Schoolaid\Fel\Certification\Contracts;

use DateTimeInterface;

/**
 * Interface for certification responses
 */
interface CertificationResponseInterface
{
    /**
     * Check if the certification was successful
     *
     * @return bool
     */
    public function isSuccessful(): bool;
    
    /**
     * Get the UUID of the certified document
     *
     * @return string|null
     */
    public function getUuid(): ?string;
    
    /**
     * Get the series of the certified document
     *
     * @return string|null
     */
    public function getSeries(): ?string;
    
    /**
     * Get the number of the certified document
     *
     * @return string|null
     */
    public function getNumber(): ?string;
    
    /**
     * Get the date of certification
     *
     * @return string|null
     */
    public function getCertificationDate(): ?string;
    
    /**
     * Get any errors that occurred during certification
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
     * Get the certified XML
     *
     * @return string|null
     */
    public function getCertifiedXml(): ?string;
    
    /**
     * Get the original XML sent in the request
     *
     * @return string|null
     */
    public function getRequestXml(): ?string;
} 