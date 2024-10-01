<?php 
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\AddendaXML;

class FELAddendas implements GeneratesXML {
    use HasXML;
    public function __construct(
        private array $addendas
    ) {}


    public function asXML(): string
    {
        $addendasSubElements = [];
        $count = 0;
        foreach ($this->addendas as $addenda) {
            $subElements = [
                AddendaXML::Name->value => $addenda->getName(),
                AddendaXML::Value->value => $addenda->getValue()
            ];

            $addendasSubElements[AddendaXML::Tag->value . (++$count)] = $subElements;
        }

        $xml = $this->buildXML(AddendaXML::Tag->value, element: $addendasSubElements);
        return $xml;
    }

}