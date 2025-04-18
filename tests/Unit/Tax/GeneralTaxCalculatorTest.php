<?php

namespace Tests\Unit\Tax;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\TaxEnum;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Services\Tax\GeneralTaxCalculator;

it('calculates IVA correctly for an item', function () {
    // Create a tax calculator
    $calculator = new GeneralTaxCalculator();
    
    // Create an item without taxes
    $item = new FelItem(
        1,              // lineNumber
        'S',            // goodOrService
        100.0,          // unitPrice
        'UND',          // unitMeasure
        'Servicio de prueba', // description
        100,            // price
        1,              // quantity
        0               // discount
    );
    
    // Verify that it has no taxes initially
    expect($item->taxes)->toBeEmpty();
    
    // Calculate taxes for the item
    $calculator->calculateItemTaxes($item, DocumentTypeEnum::LOCAL_INVOICE, new FelPhrases());
    
    // Verify that it now has taxes
    expect($item->taxes)->not->toBeEmpty()
        ->and($item->taxes)->toHaveCount(1)
        ->and($item->taxes[0]->shortName)->toBe(TaxEnum::IVA)
        ->and(round($item->taxes[0]->taxableAmount, 2))->toBe(89.29)
        ->and(round($item->taxes[0]->taxAmount, 2))->toBe(10.71)
        ->and($item->taxes[0]->shortName)->toBe(TaxEnum::IVA)
        ->and($item->taxes[0]->taxableUnitCode)->toBe(1)
        ->and($item->total)->toBe(100.0)
        ->and($item->price)->toBe(100.0);

    // Verify the calculated values (12% IVA in Guatemala)
});

it('calculates taxes for a collection of items', function () {
    // Create a tax calculator
    $calculator = new GeneralTaxCalculator();
    
    // Create two items without taxes
    $item1 = new FelItem(1, 'S', 100.0, 'UND', 'Servicio 1', 100, 1, 0);
    $item2 = new FelItem(2, 'S', 200.0, 'UND', 'Servicio 2', 200, 1, 0);
    
    // Create a collection with the items
    $items = new FelItems([$item1, $item2]);
    
    // Verify that the items have no taxes initially
    expect($item1->taxes)->toBeEmpty()
        ->and($item2->taxes)->toBeEmpty();

    // Calculate taxes for all items
    $calculator->calculateItemsTaxes($items, DocumentTypeEnum::LOCAL_INVOICE, new FelPhrases());
    
    // Verify that both now have taxes
    expect($item1->taxes)->not->toBeEmpty()
        ->and($item2->taxes)->not->toBeEmpty();

    // Calculate the total of taxes
    $taxTotal = $calculator->calculateTotalTaxes($items);
    
    // Verify that the total of taxes is the sum of the taxes of the items
    expect(round($taxTotal->totals['IVA'], 2))->toBe(32.14); // 10.71 + 21.43
});

it('skips IVA for export invoices', function () {
    // Create a tax calculator
    $calculator = new GeneralTaxCalculator();
    
    // Create an item without taxes
    $item = new FelItem(1, 'S', 100.0, 'UND', 'Servicio de exportación', 100, 1, 0);
    
    // Calculate taxes for the item with export document type
    $calculator->calculateItemTaxes($item, DocumentTypeEnum::EXPORT_INVOICE, new FelPhrases());
    
    // Verify that no taxes were added
    expect($item->taxes)->toBeEmpty();
}); 