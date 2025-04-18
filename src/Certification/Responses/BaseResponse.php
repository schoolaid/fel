<?php

namespace Schoolaid\Fel\Certification\Responses;

/**
 * Base class for all response types
 */
abstract class BaseResponse
{
    /**
     * Indicates if the operation was successful
     * 
     * @var bool
     */
    protected bool $successful = false;
    
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
     * @param bool $successful
     * @param array<int, string> $errors
     * @param mixed $rawResponse
     */
    public function __construct(bool $successful = false, array $errors = [], mixed $rawResponse = null)
    {
        $this->successful = $successful;
        $this->errors = $errors;
        $this->rawResponse = $rawResponse;
    }
    
    /**
     * Check if the operation was successful
     * 
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->successful;
    }
    
    /**
     * Get any errors that occurred
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