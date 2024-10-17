<?php
namespace SchoolAid\FEL\Enum;

enum AddressType: string {
    case Issuer   = 'dte:DireccionEmisor';
    case Customer = 'dte:DireccionReceptor';
}
