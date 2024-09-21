<?php 
namespace SchoolAid\FEL\Enum\General;

enum IVAAffiliationType: string {
    case General = 'Regimen General';
    case Small = 'Pequeno Contribuyente';
    case Exempt = 'Exento';
}