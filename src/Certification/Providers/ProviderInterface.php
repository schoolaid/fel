<?php

namespace Schoolaid\Fel\Providers;

use Schoolaid\Fel\Models\Cancellation;
use Schoolaid\Fel\Models\CancellationResponse;
use Schoolaid\Fel\Exceptions\CertificationException;

interface ProviderInterface
{
    /**
     * Cancel a document
     *
     * @param string $uuid Document UUID to cancel
     * @param string $nitIssuer NIT of the issuer
     * @param string $reason Reason for cancellation
     * @param string|null $idReceiver
     * @param string|null $documentDateTime
     * @param string|null $cancellationDateTime
     * @return CancellationResponse
     * @throws CertificationException
     */
    public function cancel(
        string $uuid,
        string $nitIssuer,
        string $reason,
        ?string $idReceiver = null,
        ?string $documentDateTime = null,
        ?string $cancellationDateTime = null
    ): CancellationResponse;

    /**
     * Cancel a document using a Cancellation model
     *
     * @param \Schoolaid\Fel\Models\Cancellation $cancellation
     * @return CancellationResponse
     * @throws CertificationException
     */
    public function cancelWithModel(\Schoolaid\Fel\Models\Cancellation $cancellation): CancellationResponse;

    /**
     * Check the status of a document
     */
    public function checkStatus(string $uuid): string;
} 