<?php

namespace Schoolaid\Fel\Certification\Responses;

use DateTimeInterface;
use Schoolaid\Fel\Certification\Contracts\CancellationResponseInterface;

/**
 * Response for cancellation operations
 */
class CancellationResponse extends BaseResponse implements CancellationResponseInterface
{
    /**
     * Date of cancellation
     * 
     * @var DateTimeInterface|null
     */
    protected ?DateTimeInterface $cancellationDate = null;
    
    /**
     * Original XML sent in the request
     * 
     * @var string|null
     */
    protected ?string $requestXml = null;
    
    /**
     * Constructor
     * 
     * @param bool $successful
     * @param DateTimeInterface|null $cancellationDate
     * @param array<int, string> $errors
     * @param mixed $rawResponse
     * @param string|null $requestXml
     */
    public function __construct(
        bool               $successful = false,
        ?DateTimeInterface $cancellationDate = null,
        array              $errors = [],
        mixed              $rawResponse = null,
        ?string            $requestXml = null
    ) {
        parent::__construct($successful, $errors, $rawResponse);
        
        $this->cancellationDate = $cancellationDate;
        $this->requestXml = $requestXml;
    }
    
    /**
     * Get the cancellation date
     * 
     * @return DateTimeInterface|null
     */
    public function getCancellationDate(): ?DateTimeInterface
    {
        return $this->cancellationDate;
    }
    
    /**
     * Get the original XML sent in the request
     * 
     * @return string|null
     */
    public function getRequestXml(): ?string
    {
        return $this->requestXml;
    }

    /**
     * Check if the cancellation was successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    /**
     * Get any errors that occurred during cancellation
     *
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the raw response from the provider
     *
     * @return mixed
     */
    public function getRawResponse(): mixed
    {
        return $this->rawResponse;
    }
} 