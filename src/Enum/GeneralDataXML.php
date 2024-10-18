<?php
namespace SchoolAid\FEL\Enum;

enum GeneralDataXML: string {
    case Tag           = 'dte:DatosGenerales';
    case IssueDateTime = 'FechaHoraEmision';
    case Type          = 'Tipo';
    case CurrencyCode  = 'CodigoMoneda';
    case AccessNumber  = 'NumeroAcceso';
    case Exportation   = 'Exp';
}
