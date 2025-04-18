<?php

namespace Schoolaid\Fel\Certification\Contracts;

use DateTimeInterface;
use Schoolaid\Fel\Certification\Responses\CancellationResponse;
use Schoolaid\Fel\Certification\Responses\CertificationResponse;
use Schoolaid\Fel\Certification\Responses\StatusResponse;
use Schoolaid\Fel\Models\Cancellation;

/**
 * Interface for FEL providers
 */
interface ProviderInterface
{
    /**
     * Certify an XML document
     *
     * @param string $xml
     * @return CertificationResponse
     */
    public function certify(string $xml): CertificationResponse;

    /**
     * Cancel a document
     *
     * @param string $xml
     * @return CancellationResponse
     */
    public function cancel(
        string $xml
    ): CancellationResponse;

    /**
     * Check the status of a document
     *
     * @param string $uuid
     * @return StatusResponse
     */
    public function checkStatus(string $uuid): StatusResponse;
} 