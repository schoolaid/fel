<?php
namespace SchoolAid\FEL\Enum;

enum IssuerXML: string {
    case Tag            = 'dte:Emisor';
    case EmailIssuer    = 'CorreoEmisor';
    case IssuerNit      = 'NITEmisor';
    case CommercialName = 'NombreComercial';
    case IssuerName     = 'NombreEmisor';
    case IVAAffiliation = 'TipoRegimen';
    case Address        = 'DireccionEmisor';
}
