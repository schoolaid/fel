<?php
namespace SchoolAid\FEL\Documents;

class Issuer
{
    public function __construct(
        public string $ivaAffiliation,
        public int $officeCode,
        public string $email,
        public string $state,
        public string $city,
        public string $address,
        public string $nit,
        public string $commercialName,
        public string $name,
        public string $infileUser,
        public string $infilePassword,
        public string $infileKey,
    ) {
        $this->state   = strtoupper($state);
        $this->city    = strtoupper($city);
        $this->address = strtoupper($address);
    }

    public function getIvaAffiliationType(): string
    {
        return $this->ivaAffiliation;
    }

}