<?php

namespace Schoolaid\Fel\Xml\Enums;

enum ItemsXmlTags: string
{
    case Tag           = 'dte:Items';
    case Item          = 'dte:Item';
    case LineNumber    = 'NumeroLinea';
    case GoodOrService = 'BienOServicio';
    case Quantity      = 'dte:Cantidad';
    case UnitMeasure   = 'dte:UnidadMedida';
    case Description   = 'dte:Descripcion';
    case UnitPrice     = 'dte:PrecioUnitario';
    case Price         = 'dte:Precio';
    case Discount      = 'dte:Descuento';
    case Taxes         = 'dte:Impuestos';
    case Tax           = 'dte:Impuesto';
    case Total         = 'dte:Total';
}