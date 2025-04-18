<?php

namespace Schoolaid\Fel\Xml\Enums;

enum TotalsXmlTags: string
{
    case Tag             = 'dte:Totales';
    case TotalTaxes      = 'dte:TotalImpuestos';
    case TotalTax        = 'dte:TotalImpuesto';
    case ShortName       = 'NombreCorto';
    case TotalTaxAmount  = 'TotalMontoImpuesto';
    case GrandTotal      = 'dte:GranTotal';
} 