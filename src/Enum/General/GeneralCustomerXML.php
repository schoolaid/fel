<?php 

namespace SchoolAid\FEL\Enum\General;

enum GeneralCustomerXML: string
{
    case Tag = 'dte:Receptor';
    case IdCustomer = 'NITReceptor';
    case EmailCustomer = 'CorreoReceptor';
    case CustomerName = 'NombreReceptor';
    case SpecialType = 'TipoEspecial';
}