<?php

namespace Schoolaid\Fel\Xml\Enums;

enum ReferenceNoteXmlTags: string
{
    case Complements   = 'dte:Complementos';
    case Complement    = 'dte:Complemento';
    case ReferenceNote = 'cno:ReferenciasNota';

    // Atributos de dte:Complemento
    case ComplementID   = 'IDComplemento';
    case ComplementName = 'NombreComplemento';
    case ComplementURI  = 'URIComplemento';

    // Atributos de cno:ReferenciasNota
    case Namespace           = 'xmlns:cno';
    case Version             = 'Version';
    case OldRegime           = 'RegimenAntiguo';
    case AuthorizationNumber = 'NumeroAutorizacionDocumentoOrigen';
    case OriginSeries        = 'SerieDocumentoOrigen';
    case OriginNumber        = 'NumeroDocumentoOrigen';
    case OriginEmissionDate  = 'FechaEmisionDocumentoOrigen';
    case AdjustmentReason    = 'MotivoAjuste';
}
