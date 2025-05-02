<?php

namespace Schoolaid\Fel\Xml\Enums;

enum GeneralDataXmlTags: string
{
    case Tag              = 'dte:DatosGenerales';
    case EmissionDateTime = 'FechaHoraEmision';
    case CurrencyCode     = 'CodigoMoneda';
    case DocumentType     = 'Tipo';
    case PersonType       = 'TipoPersoneria';
} 