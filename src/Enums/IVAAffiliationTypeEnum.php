<?php

namespace Schoolaid\Fel\Enums;

enum IVAAffiliationTypeEnum: string {
    case General = 'GEN';
    case Small   = 'PEQ';
    case Exempt  = 'EXT';
    case NonTaxable = 'EXE';
}