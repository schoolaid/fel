<?php
namespace SchoolAid\FEL\Enum;

enum CustomerXML: string {
    case Tag           = 'dte:Receptor';
    case TaxId         = 'NITReceptor';
    case EmailCustomer = 'CorreoReceptor';
    case CustomerName  = 'NombreReceptor';
    case SpecialType   = 'TipoEspecial';
}
