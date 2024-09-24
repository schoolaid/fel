<?php 
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\General\GeneralItemsXML;
use SchoolAid\FEL\Models\Items;
use SchoolAid\FEL\Models\TaxDetails;
use SchoolAid\FEL\Traits\XMLWritterTrait;

class GeneralItems implements GeneratesXML 
{
    use XMLWritterTrait;
    public function __construct(
        private Items $items,
        // private TaxDetails $taxDetails
    ) {}

    public function asXML(): string
    {
        $sub_elements = [
            GeneralItemsXML::LineNumber->value => $this->items->getLineNumber(),
            GeneralItemsXML::ProductOrService->value => $this->items->getProductOrService(),
            GeneralItemsXML::Quantity->value => $this->items->getQuantity(),
            GeneralItemsXML::UnitOfMeasurement->value => $this->items->getUnitOfMeasurement(),
            GeneralItemsXML::Description->value => $this->items->getDescription(),
            GeneralItemsXML::UnitPrice->value => $this->items->getUnitPrice(),
            GeneralItemsXML::Price->value => $this->items->getPrice(),
            GeneralItemsXML::Discount->value => $this->items->getDiscount(),
            GeneralItemsXML::Total->value => $this->items->getTotal(),
        ];

        $xml = $this->buildXML(GeneralItemsXML::Tag->value, [], $sub_elements);
        return $xml;
    }
} 