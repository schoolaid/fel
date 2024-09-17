<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralDataXML: string {
    case Tag           = 'dte:DatosGenerales';
    case IssueDateTime = 'FechaHoraEmision';
    case Type          = 'Tipo';
    case CurrencyCode  = 'CodigoMoneda';
    case AccessNumber  = 'NumeroAcceso';
    case Exportation   = 'Exp';
}
