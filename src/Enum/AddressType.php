<?php
namespace SchoolAid\FEL\Enum;

enum AddressType: string {
    case Issuer   = 'dte:DatosEmisor';
    case Customer = 'dte:DatosReceptor';
}
