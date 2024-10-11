<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Enum\ItemXML;
use SchoolAid\FEL\Enum\TaxNames;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Models\TaxDetail;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\ProductServiceType;

class FELItems implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $items,
        private ProductServiceType $productServiceType,
        // private TaxDetail $taxDetails //array de taxes
    ) {}

    public function asXML(): string
    {
        $itemsSubElements = [];
        $count            = 0;
        foreach ($this->items as $item) {

            $tax = new FELTaxDetail($item->getTotal(), TaxNames::IVA);

            $attributes = [
                ItemXML::LineNumber->value       => ++$count,
                ItemXML::ProductOrService->value => $this->productServiceType->value,
            ];

            $subElements = [

                ItemXML::Quantity->value    => $item->getQuantity(),
                ItemXML::Description->value => $item->getDescription(),
                ItemXML::UnitPrice->value   => $item->getUnitPrice(),
                ItemXML::Price->value       => $item->getPrice(),
                ItemXML::TaxDetail->value   => $tax->asXML(),
                ItemXML::Total->value       => $item->getTotal(),
            ];

            if ($item->getDiscount()) {
                $subElements[ItemXML::Discount->value] = $item->getDiscount();
            }

            if ($item->getUnitOfMeasurement()) {
                $subElements[ItemXML::UnitOfMeasurement->value] = $item->getUnitOfMeasurement();
            }

            // $itemsSubElements[ItemXML::TagSingular->value] = $subElements;
            $itemsSubElements[] = $this->buildXML(ItemXML::TagSingular->value, $attributes, $subElements);
        }

        $xml = $this->buildXML(ItemXML::TagPlural->value, element: implode("", $itemsSubElements));

        return $xml;
    }
}
