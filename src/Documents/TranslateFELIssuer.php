<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Documents\Generic\FELAddress;
use SchoolAid\FEL\Documents\Generic\FELIssuer;
use SchoolAid\FEL\Enum\AddressType;
use SchoolAid\FEL\Enum\IVAAffiliationType;
use SchoolAid\FEL\Models\Address;
use SchoolAid\FEL\Models\Issuer;

class TranslateFELIssuer
{
    public function __construct(
        public string $emailIssuer,
        public int $establishmentCode,
        public string $nitIssuer,
        public string $commercialName,
        public string $nameIssuer,
        public string $address,
        public ?string $postalCode,
        public string $city,
        public string $state,
        public ?string $country,
    )
    {
        $this->postalCode = $postalCode ?? '01001';
        $this->country = $country ?? 'GT';
    }

    public function settingFELIssuer(): FELIssuer
    {
        return new FELIssuer(
            new Issuer(
                $this->emailIssuer,
                $this->establishmentCode,
                $this->nitIssuer,
                $this->commercialName,
                $this->nameIssuer
            ),
            new FELAddress(
                new Address(
                    $this->address,
                    $this->postalCode,
                    $this->city,
                    $this->state,
                    $this->country
                ),
                AddressType::Issuer
            ),
            IVAAffiliationType::General
        );
    }
}