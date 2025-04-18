<?php

namespace Schoolaid\Fel\Certification\Providers;

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;
use DOMException;
use Schoolaid\Fel\Actions\FelCancel;
use Schoolaid\Fel\Certification\Actions\CancelAction;
use Schoolaid\Fel\Certification\Actions\CertifyAction;
use Schoolaid\Fel\Certification\Actions\StatusAction;
use Schoolaid\Fel\Certification\Contracts\ProviderInterface;
use Schoolaid\Fel\Certification\Exceptions\BodyNotSetException;
use Schoolaid\Fel\Certification\Responses\CancellationResponse;
use Schoolaid\Fel\Certification\Responses\CertificationResponse;
use Schoolaid\Fel\Certification\Responses\StatusResponse;
use Schoolaid\Fel\Config\FelConfig;
use Schoolaid\Fel\Models\Cancellation;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Infile provider implementation
 */
class InfileProvider implements ProviderInterface
{
    /**
     * FEL configuration
     * 
     * @var FelConfig
     */
    protected FelConfig $config;
    
    /**
     * Constructor
     * 
     * @param FelConfig $config
     */
    public function __construct(FelConfig $config)
    {
        $this->config = $config;
    }
    
    /**
     * Get common headers for Infile requests
     * 
     * @return array<string, string>
     */
    protected function getCommonHeaders(): array
    {
        $headers = [
            'Usuario' => $this->config->getUsername(),
            'UsuarioFirma' => $this->config->getUsername(),
            'UsuarioApi' => $this->config->getUsername(),
            'llave' => $this->config->getSignatureKey(),
            'llaveFirma' => $this->config->getApiKey(),
            'llaveApi' => $this->config->getSignatureKey(),
        ];
        
        $identifier = $this->config->getIdentifier();
        if (!empty($identifier)) {
            $headers['identificador'] = $identifier;
        }
        
        return $headers;
    }
    
    /**
     * Parse HTTP response to a standardized array
     * 
     * @param ResponseInterface $response
     * @return array<string, mixed>
     */
    protected function parseResponse(ResponseInterface $response): array
    {
        $bodyContent = (string) $response->getBody();
        $statusCode = $response->getStatusCode();
        $responseData = [];
        
        if ($response->hasHeader('Content-Type') &&
            str_contains($response->getHeaderLine('Content-Type'), 'application/json')) {
            $responseData = json_decode($bodyContent, true) ?: [];
        } else {
            $responseData = ['raw_content' => $bodyContent];
        }
        
        return [
            'code' => $statusCode,
            'body' => $responseData,
        ];
    }

    /**
     * Certify an XML document
     *
     * @param string $xml
     * @return CertificationResponse
     * @throws BodyNotSetException|GuzzleException
     */
    public function certify(string $xml): CertificationResponse
    {
        $providerConfig = $this->config->getProviderConfig();
        $action = new CertifyAction(
            $providerConfig['base_url'],
            $providerConfig['certify_url']
        );

        // Set XML body and headers
        $action->setBody($xml)
            ->setHeaders(array_merge(
                $this->getCommonHeaders(),
                ['Content-Type' => 'application/xml']
            ));
        
        // Execute the request and get raw response
        $httpResponse = $action->submit();
        
        // Parse the response
        $result = $this->parseResponse($httpResponse);
        $responseData = $result['body'];
        $statusCode = $result['code'];
        
        // Verify certification result
        if ($statusCode === 200 && isset($responseData['resultado']) && $responseData['resultado']) {
            // Successful certification
            return new CertificationResponse(
                true,
                $responseData['uuid'] ?? null,
                $responseData['serie'] ?? null,
                $responseData['numero'] ?? null,
                $responseData['fecha'],
                $responseData['xml_certificado'] ?? null,
                [],
                $responseData,
                $xml
            );
        }

        // Failed certification
        $errors = [];
        if (isset($responseData['descripcion'])) {
            $errors[] = $responseData['descripcion'];
        } elseif (isset($responseData['mensaje'])) {
            $errors[] = $responseData['mensaje'];
        } else {
            $errors[] = 'Unknown error during certification';
        }
        
        return new CertificationResponse(
            false,
            null,
            null,
            null,
            null,
            null,
            $errors,
            $responseData,
            $xml
        );
    }

    /**
     * @throws DateMalformedStringException
     * @throws GuzzleException
     * @throws BodyNotSetException
     */
    public function cancel(string $xml): CancellationResponse
    {
        $providerConfig = $this->config->getProviderConfig();

        // Create action with Infile-specific configuration
        $action = new CancelAction(
            $providerConfig['base_url'],
            $providerConfig['cancel_url']
        );
        
        // Set XML body and headers for XML content
        $action->setBody($xml)
            ->setHeaders(array_merge(
                $this->getCommonHeaders(),
                ['Content-Type' => 'application/xml']
            ));

        // Execute the request and get raw response
        $httpResponse = $action->submit();
        
        // Parse the response
        $result = $this->parseResponse($httpResponse);
        $responseData = $result['body'];
        $statusCode = $result['code'];
        
        if ($statusCode === 200 && isset($responseData['resultado']) && $responseData['resultado']) {
            return new CancellationResponse(
                true,
                isset($responseData['fecha']) ? new DateTime($responseData['fecha']) : null,
                [],
                $responseData,
                $xml
            );
        }
        
        // Failed cancellation
        $errors = [];
        if (isset($responseData['descripcion'])) {
            $errors[] = $responseData['descripcion'];
        } elseif (isset($responseData['mensaje'])) {
            $errors[] = $responseData['mensaje'];
        } else {
            $errors[] = 'Unknown error during cancellation';
        }
        
        return new CancellationResponse(
            false,
            null,
            $errors,
            $responseData,
            $xml
        );
    }

    /**
     * Check the status of a document
     * 
     * @param string $uuid
     * @return StatusResponse
     */
    public function checkStatus(string $uuid): StatusResponse
    {
        $providerConfig = $this->config->getProviderConfig();
        
        // Create action with Infile-specific configuration
        $action = new StatusAction(
            $providerConfig['base_url'],
            $providerConfig['status_url'] ?? 'consultarEstatus'
        );
        
        // For GET requests, append the UUID to the URL
        $statusUrl = $action->url() . '?uuid=' . urlencode($uuid);
        
        // Set the URL and headers
        $action->setHeaders($this->getCommonHeaders());
        
        // Execute the request and get raw response
        try {
            $httpResponse = $action->submit();
            // Parse the response
            $result = $this->parseResponse($httpResponse);
            $responseData = $result['body'];
            $statusCode = $result['code'];

            // Verify status check result
            if ($statusCode === 200 && isset($responseData['resultado']) && $responseData['resultado']) {
                // Successful status check
                return new StatusResponse(
                    true,
                    $responseData['status'] ?? null,
                    [],
                    $responseData
                );
            }

            // Failed status check
            $errors = [];
            if (isset($responseData['descripcion'])) {
                $errors[] = $responseData['descripcion'];
            } elseif (isset($responseData['mensaje'])) {
                $errors[] = $responseData['mensaje'];
            } else {
                $errors[] = 'Unknown error during status check';
            }

            return new StatusResponse(
                false,
                null,
                $errors,
                $responseData
            );
        } catch (BodyNotSetException|GuzzleException $e) {
            return new StatusResponse(
                false,
                null,
                ['Exception: ' . $e->getMessage()],
                []
            );
        }
    }
} 