<?php
namespace SchoolAid\FEL\Enum;

enum PhraseXML: string {
    case Tag   = 'dte:Phrase';
    case PhraseType = 'TipoFrase';
    case codeScenario  = 'CodigoEscenario';
    case Resolution = 'Resolucion';
    case Date = 'Fecha';
}
