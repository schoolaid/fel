<?php
namespace SchoolAid\FEL\Enum;

enum IVAAffiliationType: string {
    case General = 'Regimen General';
    case Small   = 'Pequeno Contribuyente';
    case Exempt  = 'Exento';
}
