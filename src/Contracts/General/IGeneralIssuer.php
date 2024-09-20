<?php
namespace SchoolAid\FEL\Contracts\General;
interface IGeneralIssuer
{
    public function getEmailIssuer(): string;
    public function getIssuerNit(): string;
    public function getCommercialName(): string;
    public function getIvaAffiliation(): string;
    public function getIssuerName(): string;

}
