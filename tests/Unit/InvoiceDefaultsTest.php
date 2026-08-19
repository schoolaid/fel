<?php

namespace Tests\Unit;

use Schoolaid\Fel\Models\Invoice;

it('defaults to FACT when no document type is given', function () {
    $invoice = new Invoice();

    expect($invoice->documentType)->toBe('FACT')
        ->and($invoice->currencyCode)->toBe('GTQ');
});
