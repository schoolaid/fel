<?php
namespace SchoolAid\FEL\Documents\Generic\General;

use SchoolAid\FEL\Models\Item;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\General\GeneralItemXML;
use SchoolAid\FEL\Enum\General\ProductServiceType;

class GeneralItems implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private Item $item,
        private ProductServiceType $productServiceType,
        private GeneralTaxDetail $taxDetails
    ) {}

    public function asXML(): string
    {
        $subElements = [
            GeneralItemXML::LineNumber->value       => $this->item->getLineNumber(),
            GeneralItemXML::ProductOrService->value => $this->productServiceType->value,
            GeneralItemXML::Quantity->value         => $this->item->getQuantity(),
            GeneralItemXML::Description->value      => $this->item->getDescription(),
            GeneralItemXML::UnitPrice->value        => $this->item->getUnitPrice(),
            GeneralItemXML::Price->value            => $this->item->getPrice(),
            GeneralItemXML::TaxDetail->value        => $this->taxDetails->asXML(),
            GeneralItemXML::Total->value            => $this->item->getTotal(),
        ];

        if ($this->item->getDiscount() !== null) {
            $subElements[GeneralItemXML::Discount->value] = $this->item->getDiscount();
        }

        if ($this->item->getUnitOfMeasurement() !== null) {
            $subElements[GeneralItemXML::UnitOfMeasurement->value] = $this->item->getUnitOfMeasurement();
        }

        $xml = $this->buildXML(GeneralItemXML::Tag->value, element: $subElements);

        return $xml;
    }
}
