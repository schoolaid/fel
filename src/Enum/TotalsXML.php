<?php
namespace SchoolAid\FEL\Enum;

enum TotalsXML: string {
    case Tag        = 'dte:Totales';
    case GrantTotal = 'dte:GranTotal';
    case TaxTotalPlural   = 'dte:TotalImpuestos';
    case TaxTotalSingular = 'dte:TotalImpuesto';
    case TaxAmount = 'TotalMontoImpuesto';
    case TaxShortName = 'NombreCorto';
}
