<?php
namespace SchoolAid\FEL\Contracts\General;

interface IGeneralData
{
    public function getIssueDateTime(): string;
    public function getCurrencyCode(): string;
    public function getAccessNumber(): ?string;
    public function getType(): string;
    public function getExportation(): ?string;
    public function asXML(): string;
}
