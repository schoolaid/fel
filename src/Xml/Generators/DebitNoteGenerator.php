<?php

namespace Schoolaid\Fel\Xml\Generators;

/**
 * Generator for debit notes (NDEB)
 *
 * Comparte las reglas de la nota de crédito: mismo complemento ReferenciasNota
 * obligatorio; solo cambian Tipo y NombreComplemento (resueltos por tipo).
 */
class DebitNoteGenerator extends CreditNoteGenerator
{
}
