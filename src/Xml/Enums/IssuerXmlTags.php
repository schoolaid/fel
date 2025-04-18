<?php

namespace Schoolaid\Fel\Xml\Enums;

enum IssuerXmlTags: string
{
    case Tag                = 'dte:Emisor';
    case Email              = 'CorreoEmisor';
    case EstablishmentCode = 'CodigoEstablecimiento';
    case TaxId              = 'NITEmisor';
    case CommercialName     = 'NombreComercial';
    case VatAffiliation     = 'AfiliacionIVA';
    case Name               = 'NombreEmisor';
    case Address            = 'dte:DireccionEmisor';
} 