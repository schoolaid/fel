<?php
namespace SchoolAid\FEL\Contracts;
use SchoolAid\FEL\Contracts\GeneratesXML;

interface IGeneralData extends GeneratesXML
{
    public function getIssueDateTime(): string;
    public function getCurrencyCode(): string;
    public function getAccessNumber(): ?string;
    public function getType(): string;
    public function getExportation(): ?string;
       
}
