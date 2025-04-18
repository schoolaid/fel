<?php

namespace Schoolaid\Fel\Xml\Enums;

enum ReceiverXmlTags: string
{
    case Tag           = 'dte:Receptor';
    case TaxId         = 'IDReceptor';
    case EmailCustomer = 'CorreoReceptor';
    case CustomerName  = 'NombreReceptor';
    case SpecialType   = 'TipoEspecial';
}