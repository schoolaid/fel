<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Enum\ItemXML;
use SchoolAid\FEL\Enum\ProductServiceType;
use SchoolAid\FEL\Models\TaxDetail;

class FELItems implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $items,
        private ProductServiceType $productServiceType,
        // private FELTaxDetail $taxDetails
    ) {}

    public function asXML(): string
    {
        $itemsSubElements = [];
        $count = 0;
        foreach ($this->items as $item) {

            $subElements = [
                ItemXML::LineNumber->value       => $item->getLineNumber(),
                ItemXML::ProductOrService->value => $this->productServiceType->value,
                ItemXML::Quantity->value         => $item->getQuantity(),
                ItemXML::Description->value      => $item->getDescription(),
                ItemXML::UnitPrice->value        => $item->getUnitPrice(),
                ItemXML::Price->value            => $item->getPrice(),
                // ItemXML::TaxDetail->value        => $this->taxDetails->asXML(),
                ItemXML::Total->value            => $item->getTotal(),
            ];
    
            if ($item->getDiscount()) {
                $subElements[ItemXML::Discount->value] = $item->getDiscount();
            }
    
            if ($item->getUnitOfMeasurement()) {
                $subElements[ItemXML::UnitOfMeasurement->value] = $item->getUnitOfMeasurement();
            }
            
            $itemsSubElements[ItemXML::Tag->value . (++$count)] = $subElements;
        }

        $xml = $this->buildXML(ItemXML::Tag->value, element: $itemsSubElements);

        return $xml;
    }
}
