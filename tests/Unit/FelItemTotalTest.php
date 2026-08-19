<?php

namespace Tests\Unit;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Services\Tax\GeneralTaxCalculator;

it('defaults the total to price minus discount when not provided', function () {
    $item = new FelItem(1, 'S', 100.0, 'UND', 'Servicio', 100.0, 1, 10.0);

    expect($item->total)->toBe(90.0);
});

it('keeps an explicitly provided total', function () {
    $item = new FelItem(1, 'S', 100.0, 'UND', 'Servicio', 100.0, 1, 0.0, [], 100.0);

    expect($item->total)->toBe(100.0);
});

it('calculates IVA over the defaulted total including the discount', function () {
    $item = new FelItem(1, 'S', 100.0, 'UND', 'Servicio', 100.0, 1, 10.0);
    $items = new FelItems([$item]);

    (new GeneralTaxCalculator())->calculateItemsTaxes($items, DocumentTypeEnum::LOCAL_INVOICE, new FelPhrases());

    expect($item->total)->toBe(90.0)
        ->and(round($item->taxes[0]->taxableAmount, 2))->toBe(80.36)
        ->and(round($item->taxes[0]->taxAmount, 2))->toBe(9.64)
        ->and($items->calculateTotal())->toBe(90.0);
});
