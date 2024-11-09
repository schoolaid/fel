<?php
namespace SchoolAid\FEL\Documents\Generic;

use SchoolAid\FEL\Enum\ItemXML;
use SchoolAid\FEL\Enum\TaxNames;
use SchoolAid\FEL\Traits\HasXML;
use SchoolAid\FEL\Contracts\GeneratesXML;
use SchoolAid\FEL\Enum\ProductServiceType;

class FELItems implements GeneratesXML
{
    use HasXML;
    public function __construct(
        private array $items,
        // private ProductServiceType $productServiceType,
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

                ItemXML::Quantity->value          => $item->getQuantity(),
                ItemXML::UnitOfMeasurement->value => $item->getUnitOfMeasurement(),
                ItemXML::Description->value       => $item->getDescription(),
                ItemXML::UnitPrice->value         => $item->getUnitPrice(),
                ItemXML::Price->value             => $item->getPrice(),
                ItemXML::Discount->value          => $item->getDiscount(),
                ItemXML::TaxDetail->value         => $tax->asXML(),
                ItemXML::Total->value             => $item->getTotal(),
            ];

            $itemsSubElements[] = $this->buildXML(ItemXML::TagSingular->value, $attributes, $subElements);
        }

        $xml = $this->buildXML(ItemXML::TagPlural->value, element: implode("", $itemsSubElements));

        return $xml;
    }
}
