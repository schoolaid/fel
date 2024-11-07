<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Contracts\InfileProvider;

class TranslateFELSections implements InfileProvider
{
    public function __construct(public string $section, public string $sectionName, public string $sectionDescription)
    {}

    // public function format()
    // {
    //     return [
    //         'section' => $this->section,
    //         'sectionName' => $this->sectionName,
    //         'sectionDescription' => $this->sectionDescription,
    //     ];
    // }

    public function passingFormat(): string
    {
        return "";
    }
}