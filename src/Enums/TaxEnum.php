<?php

namespace Schoolaid\Fel\Enums;

enum TaxEnum: string {
    case IVA    = 'IVA';
    case ISO    = 'ISO';
    case RETIVA = 'RETENCION IVA';
    case ISR    = 'ISR';

    public function percentage(): float {

        return match ($this) {
            TaxEnum::IVA    => 0.12,
            TaxEnum::ISO, TaxEnum::ISR, TaxEnum::RETIVA => 0.01,
        };
    }

    public function taxableCode(): int {
        return match ($this) {
            TaxEnum::IVA    => 1,
            TaxEnum::ISO    => 2,
            TaxEnum::RETIVA => 3,
            TaxEnum::ISR    => 4,
        };
    }
}