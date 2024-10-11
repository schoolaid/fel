<?php
namespace SchoolAid\FEL\Enum;

enum TaxDetailXML: string {
    case Tag              = 'dte:Impuesto';
    case ShortName        = 'dte:NombreCorto';
    case TaxableUnitCode  = 'dte:CodigoUnidadGravable';
    case TaxableAmount    = 'dte:MontoGravable';
    case TaxableUnitCount = 'dte:CantidadUnidadesGravables';
    case TaxAmount        = 'dte:MontoImpuesto';
    case Total            = 'dte:total';
}
