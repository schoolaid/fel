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

        // return implode("\n", $xmlParts);
        $dynamicContent = implode("\n", $xmlParts);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" ';
        $xml .= 'xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" ';
        $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="0.1" ';
        $xml .= 'xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.1.0">' . "\n";
        $xml .= '    <dte:SAT ClaseDocumento="dte">' . "\n";
        $xml .= '        <dte:DTE ID="DatosCertificados">' . "\n";
        $xml .= '            <dte:DatosEmision ID="DatosEmision">' . "\n";
        $xml .= $dynamicContent;
        $xml .= '            </dte:DatosEmision>' . "\n";
        $xml .= '        </dte:DTE>' . "\n";
        $xml .= '    </dte:SAT>' . "\n";
        $xml .= '</dte:GTDocumento>' . "\n";

        return $xml;
    }
}
