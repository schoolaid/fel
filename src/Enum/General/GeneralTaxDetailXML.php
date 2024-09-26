<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralTaxDetailXML: string {
    case Tag              = 'detalleImpuesto';
    case ShortName        = 'nombreCorto';
    case TaxableUnitCode  = 'codigoUnidadgravable';
    case TaxableAmount    = 'montoGravable';
    case TaxableUnitCount = 'cantidadUnidadesGravables';
    case TaxAmount        = 'montoImpuesto';
    case Total            = 'total';
}
