<?php
namespace SchoolAid\FEL\Enum;

enum CustomerXML: string {
    case Tag           = 'dte:Receptor';
    case TaxId         = 'IDReceptor';
    case EmailCustomer = 'CorreoReceptor';
    case CustomerName  = 'NombreReceptor';
    case SpecialType   = 'TipoEspecial';
}
