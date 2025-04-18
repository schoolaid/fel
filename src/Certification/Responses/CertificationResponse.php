<?php

namespace Schoolaid\Fel\Certification\Responses;

use Schoolaid\Fel\Certification\Contracts\CertificationResponseInterface;

/**
 * Response for certification operations
 */
class CertificationResponse extends BaseResponse implements CertificationResponseInterface
{
    /**
     * UUID of the certified document
     * 
     * @var string|null
     */
    protected ?string $uuid = null;
    
    /**
     * Series of the certified document
     * 
     * @var string|null
     */
    protected ?string $series = null;
    
    /**
     * Number of the certified document
     * 
     * @var string|null
     */
    protected ?string $number = null;
    
    /**
     * Date of certification
     * 
     * @var string|null
     */
    protected ?string $certificationDate = null;
    
    /**
     * Certified XML
     * 
     * @var string|null
     */
    protected ?string $certifiedXml = null;
    
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
     * @param string|null $uuid
     * @param string|null $series
     * @param string|null $number
     * @param string|null $certificationDate
     * @param string|null $certifiedXml
     * @param array<int, string> $errors
     * @param mixed $rawResponse
     * @param string|null $requestXml
     */
    public function __construct(
        bool               $successful = false,
        ?string            $uuid = null,
        ?string            $series = null,
        ?string            $number = null,
        ?string            $certificationDate = null,
        ?string            $certifiedXml = null,
        array              $errors = [],
        mixed              $rawResponse = null,
        ?string            $requestXml = null
    ) {
        parent::__construct($successful, $errors, $rawResponse);
        
        $this->uuid = $uuid;
        $this->series = $series;
        $this->number = $number;
        $this->certificationDate = $certificationDate;
        $this->certifiedXml = $certifiedXml;
        $this->requestXml = $requestXml;
    }
    
    /**
     * Get the UUID of the certified document
     * 
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
    
    /**
     * Get the series of the certified document
     * 
     * @return string|null
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }
    
    /**
     * Get the number of the certified document
     * 
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }
    
    /**
     * Get the date of certification
     * 
     * @return string|null
     */
    public function getCertificationDate(): ?string
    {
        return $this->certificationDate;
    }
    
    /**
     * Get the certified XML
     * 
     * @return string|null
     */
    public function getCertifiedXml(): ?string
    {
        return $this->certifiedXml;
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
} 