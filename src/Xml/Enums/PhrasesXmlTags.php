<?php

namespace Schoolaid\Fel\Xml\Enums;

enum PhrasesXmlTags: string
{
    case Tag           = 'dte:Frases';
    case Phrase        = 'dte:Frase';
    case ScenarioCode  = 'CodigoEscenario';
    case PhraseType    = 'TipoFrase';
} 