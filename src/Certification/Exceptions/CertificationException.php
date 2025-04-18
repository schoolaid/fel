<?php

namespace Schoolaid\Fel\Certification\Exceptions;

/**
 * Base exception for certification process
 */
class CertificationException extends \Exception
{
    /**
     * Array of errors from the provider
     *
     * @var array<int, string>
     */
    protected array $errors = [];
    
    /**
     * Raw response from the provider
     *
     * @var mixed
     */
    protected mixed $rawResponse = null;
    
    /**
     * Constructor
     *
     * @param string $message
     * @param array<int, string> $errors
     * @param mixed $rawResponse
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        array $errors = [],
        mixed $rawResponse = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
        $this->rawResponse = $rawResponse;
    }
    
    /**
     * Get the errors from the provider
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