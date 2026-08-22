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
     * El mapeo usa los accesores veraces de FelConfig. OJO: los nombres
     * históricos de FelConfig están invertidos (getLlaveFirma() lee el slot
     * apiKey y getLlaveApi() el slot signatureKey); ver FelConfig. NO cambiar
     * qué slot alimenta cada header sin coordinar con todos los consumidores
     * del paquete: romperia la autenticación contra INFILE. El contrato está
     * fijado por el test InfileHeadersTest.
     *
     * @return array<string, string>
     */
    protected function getCommonHeaders(): array
    {
        $headers = [
            'Usuario' => $this->config->getUsername(),
            'UsuarioFirma' => $this->config->getUsername(),
            'UsuarioApi' => $this->config->getUsername(),
            'llave' => $this->config->getLlaveApi(),
            'llaveFirma' => $this->config->getLlaveFirma(),
            'llaveApi' => $this->config->getLlaveApi(),
        ];
        
        $identifier = $this->config->getIdentifier();
        if (!empty($identifier)) {
            $headers['identificador'] = $identifier;
        }
        
        return $headers;
    }
    
    /**
     * Build the HTTP client configuration from the provider config
     *
     * Traduce timeout/verify_ssl a opciones de Guzzle y permite pasar
     * opciones adicionales del cliente vía provider_config['client_config']
     * (p. ej. un handler de pruebas).
     *
     * @return array<string, mixed>
     */
    protected function getClientConfig(): array
    {
        $providerConfig = $this->config->getProviderConfig();
        $clientConfig = $providerConfig['client_config'] ?? [];

        if (isset($providerConfig['timeout'])) {
            $clientConfig['timeout'] = (float) $providerConfig['timeout'];
        }

        if (isset($providerConfig['verify_ssl'])) {
            $clientConfig['verify'] = filter_var($providerConfig['verify_ssl'], FILTER_VALIDATE_BOOLEAN);
        }

        return $clientConfig;
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
     * Traduce la respuesta de error de INFILE a una lista de mensajes.
     *
     * `descripcion` siempre trae el mismo texto genérico ("Existen errores en
     * la validacion del XML...") y el detalle real de la SAT viaja en
     * `descripcion_errores` (lista de objetos con `mensaje_error`). Sin este
     * detalle la app consumidora no puede saber por qué se rechazó el DTE.
     *
     * @param array<string, mixed> $responseData
     * @return array<int, string>
     */
    protected function extractErrors(array $responseData, string $fallback): array
    {
        $errors = [];

        foreach ((array) ($responseData['descripcion_errores'] ?? []) as $error) {
            if (is_array($error)) {
                $message = $error['mensaje_error'] ?? json_encode($error, JSON_UNESCAPED_UNICODE);
            } else {
                $message = $error;
            }

            $message = trim((string) $message);
            if ($message !== '') {
                $errors[] = $message;
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        if (!empty($responseData['descripcion'])) {
            return [(string) $responseData['descripcion']];
        }

        if (!empty($responseData['mensaje'])) {
            return [(string) $responseData['mensaje']];
        }

        return [$fallback];
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
            $providerConfig['certify_url'],
            $this->getClientConfig()
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
                $responseData['fecha'] ?? null,
                $responseData['xml_certificado'] ?? null,
                [],
                $responseData,
                $xml
            );
        }

        // Failed certification
        $errors = $this->extractErrors($responseData, 'Unknown error during certification');
        
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
            $providerConfig['cancel_url'],
            $this->getClientConfig()
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
        $errors = $this->extractErrors($responseData, 'Unknown error during cancellation');
        
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
            $providerConfig['status_url'] ?? 'consultarEstatus',
            $this->getClientConfig()
        );
        
        // For GET requests, the UUID travels in the URL
        $action->setUuid($uuid);

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
            $errors = $this->extractErrors($responseData, 'Unknown error during status check');

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