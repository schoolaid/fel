<?php

namespace Schoolaid\Fel\Certification\Actions;

use Schoolaid\Fel\Certification\Actions\Interfaces\FelAction;
use Schoolaid\Fel\Certification\Exceptions\BodyNotSetException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for FEL actions - handles only HTTP communication
 */
abstract class BaseFelAction implements FelAction
{
    /**
     * HTTP method to use
     * 
     * @var string
     */
    protected string $method = 'POST';
    
    /**
     * Request body
     * 
     * @var mixed
     */
    protected $body;
    
    /**
     * Request headers
     * 
     * @var array<string, string>
     */
    protected array $headers = [];
    
    /**
     * HTTP client
     * 
     * @var Client
     */
    protected Client $client;
    
    /**
     * Base URL for requests
     * 
     * @var string
     */
    protected string $baseUrl;
    
    /**
     * Constructor
     * 
     * @param string $baseUrl Base URL for requests
     * @param array<string, mixed> $clientConfig Additional HTTP client configuration
     */
    public function __construct(string $baseUrl, array $clientConfig = [])
    {
        $this->baseUrl = $baseUrl;
        
        // Merge default config with provided config
        $config = array_merge([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'verify' => true,
        ], $clientConfig);
        
        $this->client = new Client($config);
    }
    
    /**
     * Get method
     * 
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }
    
    /**
     * Set body
     * 
     * @param mixed $body
     * @return self
     */
    public function setBody(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }
    
    /**
     * Set headers
     * 
     * @param array<string, string> $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
    /**
     * Submit the request and return the raw HTTP response
     * 
     * @return ResponseInterface
     * @throws BodyNotSetException|GuzzleException
     */
    public function submit(): ResponseInterface
    {
        if (!isset($this->body) && $this->method !== 'GET') {
            throw new BodyNotSetException('Body not set for non-GET request');
        }
        
        $options = ['headers' => $this->headers];
        
        if (isset($this->body)) {
            if (isset($this->headers['Content-Type'])) {
                if (str_contains($this->headers['Content-Type'], 'application/json')) {
                    $options['json'] = $this->body;
                } else {
                    $options['body'] = $this->body;
                }
            } else {
                $options['body'] = $this->body;
            }
        }
        
        return $this->client->request($this->method(), $this->url(), $options);
    }
} 