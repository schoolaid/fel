<?php

namespace Schoolaid\Fel\Xml\Enums;

enum AddressXmlTags: string
{
    case Address     = 'dte:Direccion';
    case PostalCode  = 'dte:CodigoPostal';
    case Municipality = 'dte:Municipio';
    case Department  = 'dte:Departamento';
    case Country     = 'dte:Pais';
} 