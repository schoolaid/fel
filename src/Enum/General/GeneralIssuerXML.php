<?php
namespace SchoolAid\FEL\Enum\General;

enum GeneralIssuerXML: string
{
    case EmailIssuer = 'CorreoEmisor';
    case IssuerNit = 'NITEmisor';
    case CommercialName = 'NombreComercial';
    case IvaAffiliation = 'AfiliacionIVA';
    case IssuerName = 'NombreEmisor';
}