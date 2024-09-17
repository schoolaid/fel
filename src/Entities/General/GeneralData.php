<?php

namespace SchoolAid\FEL\Entities\General;

use SchoolAid\FEL\Contracts\General\IGeneralData;

abstract class GeneralData implements IGeneralData
{
    protected string $issue_date_time;
    protected string $access_number;
    protected string $currency_code;
    protected string $type;
    protected bool $exportation;

    public function __construct(
       string $issue_date_time,
       string $access_number,
       string $currency_code,
       string $type,
       bool $exportation
    ) {
        $this->issue_date_time = $issue_date_time;
        $this->access_number = $access_number;
        $this->currency_code = $currency_code;
        $this->type = $type;
        $this->exportation = $exportation;
    }
}