<?php
namespace SchoolAid\FEL\Contracts\General;

interface IGeneralData
{
    public function issue_date_time(): string;
    public function access_number(): string;
    public function currency_code(): string;
    public function type(): string;
    public function exportation(): bool;
}