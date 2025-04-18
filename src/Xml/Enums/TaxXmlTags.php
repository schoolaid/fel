<?php

namespace Schoolaid\Fel\Xml\Enums;

enum TaxXmlTags: string
{
    case ShortName         = 'dte:NombreCorto';
    case TaxableUnitCode   = 'dte:CodigoUnidadGravable';
    case TaxableAmount     = 'dte:MontoGravable';
    case TaxAmount         = 'dte:MontoImpuesto';
} 