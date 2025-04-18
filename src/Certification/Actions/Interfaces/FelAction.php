<?php

namespace Schoolaid\Fel\Certification\Actions\Interfaces;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface for FEL actions
 */
interface FelAction
{
    /**
     * Get the URL for the action
     * 
     * @return string
     */
    public function url(): string;
    
    /**
     * Get the HTTP method for the action
     * 
     * @return string
     */
    public function method(): string;
    
    /**
     * Submit the HTTP request and return the raw HTTP response
     * 
     * @return ResponseInterface
     */
    public function submit(): ResponseInterface;
    
    /**
     * Set the body for the request
     * 
     * @param mixed $body
     * @return self
     */
    public function setBody(mixed $body): self;
    
    /**
     * Set the headers for the request
     * 
     * @param array<string, string> $headers
     * @return self
     */
    public function setHeaders(array $headers): self;
} 