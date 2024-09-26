<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralAddressXML: string {
    case Address    = 'dte:Direccion';
    case PostalCode = 'dte:CodigoPostal';
    case City       = 'dte:Municipio';
    case State      = 'dte:Departamento';
    case Country    = 'dte:Pais';
}
