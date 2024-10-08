<?php
namespace SchoolAid\FEL\Enum;

enum PhraseXML: string {
    case TagPlural = 'dte:Frases';
    case TagSingular   = 'dte:Frase';
    case PhraseType = 'TipoFrase';
    case codeScenario  = 'CodigoEscenario';
    case Resolution = 'Resolucion';
    case Date = 'Fecha';
}
