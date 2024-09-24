<?php 

namespace SchoolAid\FEL\Enum\General;

enum GeneralCustomerXML: string
{
    case Tag = 'dte:Receptor';
    case EmailCustomer = 'CorreoReceptor';
    case CustomerName = 'NombreReceptor';
    case SpecialType = 'TipoEspecial';
}