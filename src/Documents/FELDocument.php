<?php
namespace SchoolAid\FEL\Documents;

use SchoolAid\FEL\Contracts\GetSectionsXML;


class FELDocument implements GetSectionsXML
{
    public function __construct(
        private array $sections
    ) {}

    public function getSections(): array
    {
        return $this->sections;
    }

    public function generateXML(): string
    {
        $xmlParts = [];

        foreach ($this->getSections() as $section) {
            $xmlParts[] = $section->asXML();
        }

        return implode("\n", $xmlParts); 
    }
}