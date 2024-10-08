<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\AddendaXML;
use SchoolAid\FEL\Contracts\GeneratesXML;

class FELAddendas implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $addendas
    ) {}

    public function asXML(): string
    {
        $subElements = [];
    
        foreach ($this->addendas as $addenda) {
            $subElements[$addenda->getName()] = $addenda->getValue();
        }
        
        $xml = $this->buildXML(AddendaXML::Tag->value, element: $subElements);
    
        return $xml;
    }

}
