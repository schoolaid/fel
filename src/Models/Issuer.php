<?php 
namespace SchoolAid\FEL\Models;

class Issuer {
    public function __construct(
        private string $emailIssuer,
        private string $issuerNit,
        private string $commercialName,
        private string $issuerName
    ) {}

    public function getEmailIssuer(): string
    {
        return $this->emailIssuer;
    }

    public function getIssuerNit(): string
    {
        return $this->issuerNit;
    }

    public function getCommercialName(): string
    {
        return $this->commercialName;
    }

    public function getIssuerName(): string
    {
        return $this->issuerName;
    }
}