<?php

namespace Schoolaid\Fel\Models;

use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;

class FelIssuer
{
    public string $email;
    public string $establishmentCode;
    public string $nit;
    public string $commercialName;
    public IVAAffiliationTypeEnum $vatAffiliation;
    public string $name;
    public FelAddress $address;
    
    public function __construct(
        string                 $email = '',
        string                 $establishmentCode = '1',
        string                 $nit = '',
        string                 $commercialName = '',
        IVAAffiliationTypeEnum $vatAffiliation =  IVAAffiliationTypeEnum::General,
        string                 $name = '',
        ?FelAddress $address = null
    ) {
        $this->email = $email;
        $this->establishmentCode = $establishmentCode;
        $this->nit = $nit;
        $this->commercialName = $commercialName;
        $this->vatAffiliation = $vatAffiliation;
        $this->name = $name;
        $this->address = $address ?? new FelAddress();
    }
    
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'establishmentCode' => $this->establishmentCode,
            'nit' => $this->nit,
            'commercialName' => $this->commercialName,
            'vatAffiliation' => $this->vatAffiliation,
            'name' => $this->name,
            'address' => $this->address->toArray()
        ];
    }
} 