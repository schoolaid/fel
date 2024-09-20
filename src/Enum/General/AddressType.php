<?php
namespace SchoolAid\FEL\Enum\General;


enum AddressType: string {
    case Issuer           = 'dte:DatosEmisor';
    case Customer = 'dte:DatosReceptor';
}   
