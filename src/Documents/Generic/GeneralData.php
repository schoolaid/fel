<?php
namespace SchoolAid\FEL\Documents\Generic;

use DateTime;
use DateTimeZone;
use SchoolAid\FEL\Enum\GeneralDataXML;
use SchoolAid\FEL\Contracts\IGeneralData;

abstract class GeneralData implements IGeneralData
{
    public function getIssueDateTime(): string
    {
        $date = new DateTime('now', new DateTimeZone('America/Guatemala'));
        return $date->format('Y-m-d\TH:i:sP');
    }

    public function getCurrencyCode(): string
    {
        return 'GTQ';
    }

    public function getAccessNumber(): ?string
    {
        return null;
    }

    public function getExportationTag(): string
    {
        return '';
    }

    public function isExportation(): bool
    {
        return false;
    }

    public function asXML(): string
    {
        $xw = xmlwriter_open_memory();
        xmlwriter_set_indent($xw, 1);
        xmlwriter_start_element($xw, GeneralDataXML::Tag->value);

        $attributes = [
            GeneralDataXML::IssueDateTime->value => $this->getIssueDateTime(),
            GeneralDataXML::Type->value          => $this->getType(),
            GeneralDataXML::CurrencyCode->value  => $this->getCurrencyCode(),
        ];

        if ($this->getAccessNumber()) {
            $attributes[GeneralDataXML::AccessNumber->value] = $this->getAccessNumber();
        }

        if ($this->getExportation()) {
            $attributes[GeneralDataXML::Exportation->value] = $this->getExportation();
        }

        foreach ($attributes as $key => $value) {
            xmlwriter_start_attribute($xw, $key);
            xmlwriter_text($xw, $value);
            xmlwriter_end_attribute($xw);
        }

        xmlwriter_end_element($xw);

        return xmlwriter_output_memory($xw);
    }
}
