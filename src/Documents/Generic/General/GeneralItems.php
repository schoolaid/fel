<?php

namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\General\GeneralItemXML;
use SchoolAid\FEL\Enum\General\ProductServiceType;
use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Models\TaxDetails;
use SchoolAid\FEL\Traits\XMLWritterTrait;

class GeneralItems implements GeneratesXML
{
    use XMLWritterTrait;
    public function __construct(
        private Item $item,
        private ProductServiceType $productServiceType,
        // private TaxDetails $taxDetails
    ) {}

    public function asXML(): string
    {
        $sub_elements = [
            GeneralItemXML::LineNumber->value => $this->item->getLineNumber(),
            GeneralItemXML::ProductOrService->value => $this->productServiceType->value,
            GeneralItemXML::Quantity->value => $this->item->getQuantity(),
            GeneralItemXML::Description->value => $this->item->getDescription(),
            GeneralItemXML::UnitPrice->value => $this->item->getUnitPrice(),
            GeneralItemXML::Price->value => $this->item->getPrice(),
            GeneralItemXML::Total->value => $this->item->getTotal(),
        ];

        if($this->item->getDiscount() !== null) {
            $sub_elements[GeneralItemXML::Discount->value] = $this->item->getDiscount();
        }

        if($this->item->getUnitOfMeasurement() !== null) {
            $sub_elements[GeneralItemXML::UnitOfMeasurement->value] = $this->item->getUnitOfMeasurement();
        }


        $xml = $this->buildXML(GeneralItemXML::Tag->value, element: $sub_elements);
        return $xml;
    }
}
