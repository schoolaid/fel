<?php

namespace Schoolaid\Fel\Models;

use DateTimeInterface;

/**
 * Model representing cancellation data for a document
 */
class Cancellation
{
    /**
     * Constructor
     *
     * @param string $documentUuid The UUID of the document to be cancelled
     * @param string $nitIssuer The NIT of the issuer
     * @param string $idReceiver The ID of the receiver
     * @param string $reason The reason for cancellation
     * @param string $documentDateTime The date and time of the document to be cancelled
     * @param string|null $cancellationDateTime The date and time of cancellation (current time if null)
     */
    public function __construct(
        protected string $documentUuid,
        protected string $nitIssuer,
        protected string $idReceiver,
        protected string $reason,
        protected string $documentDateTime,
        protected ?string $cancellationDateTime = null
    ) {
        if (null === $this->cancellationDateTime) {
            $this->cancellationDateTime = (new \DateTime())->format('Y-m-d\TH:i:s');
        }
    }

    /**
     * Get the UUID of the document to be cancelled
     *
     * @return string
     */
    public function getDocumentUuid(): string
    {
        return $this->documentUuid;
    }

    /**
     * Get the NIT of the issuer
     *
     * @return string
     */
    public function getNitIssuer(): string
    {
        return $this->nitIssuer;
    }

    /**
     * Get the ID of the receiver
     *
     * @return string
     */
    public function getIdReceiver(): string
    {
        return $this->idReceiver;
    }

    /**
     * Get the reason for cancellation
     *
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Get the date and time of the document to be cancelled
     *
     * @return string
     */
    public function getDocumentDateTime(): string
    {
        return $this->documentDateTime;
    }

    /**
     * Get the date and time of cancellation
     *
     * @return string
     */
    public function getCancellationDateTime(): string
    {
        return $this->cancellationDateTime;
    }
} 