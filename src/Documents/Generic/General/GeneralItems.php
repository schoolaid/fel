<?php

namespace SchoolAid\FEL\Documents\Generic\General;

use ProductServiceType;
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
        private ProductServiceType $productServiceType,
        // private TaxDetails $taxDetails
    ) {}

    public function asXML(): string
    {
        $sub_elements = [
            GeneralItemsXML::LineNumber->value => $this->items->getLineNumber(),
            GeneralItemsXML::ProductOrService->value => $this->productServiceType->value,
            GeneralItemsXML::Quantity->value => $this->items->getQuantity(),
            GeneralItemsXML::Description->value => $this->items->getDescription(),
            GeneralItemsXML::UnitPrice->value => $this->items->getUnitPrice(),
            GeneralItemsXML::Price->value => $this->items->getPrice(),
            GeneralItemsXML::Total->value => $this->items->getTotal(),
        ];

        if($this->items->getDiscount() !== null) {
            $sub_elements[GeneralItemsXML::Discount->value] = $this->items->getDiscount();
        }

        if($this->items->getUnitOfMeasurement() !== null) {
            $sub_elements[GeneralItemsXML::UnitOfMeasurement->value] = $this->items->getUnitOfMeasurement();
        }


        $xml = $this->buildXML(GeneralItemsXML::Tag->value, [], $sub_elements);
        return $xml;
    }
}
