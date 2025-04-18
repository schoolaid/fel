<?php

namespace Schoolaid\Fel\Xml\Enums;

enum DocumentXmlTags: string
{
    case GTDocument   = 'dte:GTDocumento';
    case SAT          = 'dte:SAT';
    case DTE          = 'dte:DTE';
    case EmissionData = 'dte:DatosEmision';
    
    // Atributos
    case Version              = 'Version';
    case SchemaLocation       = 'xsi:schemaLocation';
    case DocumentClass        = 'ClaseDocumento';
    case ID                   = 'ID';
    case CertifiedData        = 'DatosCertificados';
    case EmissionDataID       = 'DatosEmision';
} 