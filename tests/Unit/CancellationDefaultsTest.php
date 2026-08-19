<?php

namespace Tests\Unit;

use Schoolaid\Fel\Models\Cancellation;

it('defaults the cancellation datetime to now as a string', function () {
    $cancellation = new Cancellation(
        '11AA22BB-C3D4-E5F6-A7B8-90CD12EF34AB',
        '120035502',
        'CF',
        'Anulación por devolución',
        '2026-01-01T00:00:00'
    );

    expect($cancellation->getCancellationDateTime())
        ->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/');
});
