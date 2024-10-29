<?php
namespace SchoolAid\FEL\Enum;

enum IssuerXML: string {
    case Tag            = 'dte:Emisor';
    case EmailIssuer    = 'CorreoEmisor';
    case IssuerNit      = 'NITEmisor';
    case CommercialName = 'NombreComercial';
    case IssuerName     = 'NombreEmisor';
    case EstablishmentCode  = 'CodigoEstablecimiento';
    case IVAAffiliation = 'AfiliacionIVA';
}
