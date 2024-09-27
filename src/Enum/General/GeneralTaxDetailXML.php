<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralTaxDetailXML: string {
    case Tag              = 'dte:detalleImpuesto';
    case ShortName        = 'dte:nombreCorto';
    case TaxableUnitCode  = 'dte:codigoUnidadgravable';
    case TaxableAmount    = 'dte:montoGravable';
    case TaxableUnitCount = 'dte:cantidadUnidadesGravables';
    case TaxAmount        = 'dte:montoImpuesto';
    case Total            = 'dte:total';
}
