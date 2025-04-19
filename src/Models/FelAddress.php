<?php

namespace Schoolaid\Fel\Models;

class FelAddress
{
    public string $street;
    public string $postalCode;
    public string $municipality;
    public string $department;
    public string $country;
    
    public function __construct(
        string $street = '',
        string $postalCode = '',
        string $municipality = '',
        string $department = '',
        string $country = ''
    ) {
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->municipality = $municipality;
        $this->department = $department;
        $this->country = $country;
    }
    
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'postalCode' => $this->postalCode,
            'municipality' => $this->municipality,
            'department' => $this->department,
            'country' => $this->country
        ];
    }
} 