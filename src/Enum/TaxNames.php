<?php
namespace SchoolAid\FEL\Enum;

enum TaxNames: string {
    case IVA    = 'IVA';
    case ISO    = 'ISO';
    case RETIVA = 'RETENCION IVA';
    case ISR    = 'ISR';

    public function percentage(): float {

        return match ($this) {
            TaxNames::IVA    => 0.12,
            TaxNames::ISO    => 0.01,
            TaxNames::RETIVA => 0.01,
            TaxNames::ISR    => 0.01,
        };
    }

    public function taxableCode(): int {
        return match ($this) {
            TaxNames::IVA    => 1,
            TaxNames::ISO    => 2,
            TaxNames::RETIVA => 3,
            TaxNames::ISR    => 4,
        };
    }
}
