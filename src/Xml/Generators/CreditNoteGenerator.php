<?php

namespace Schoolaid\Fel\Xml\Generators;

use Schoolaid\Fel\Exceptions\XmlGenerationException;

/**
 * Generator for credit notes (NCRE)
 */
class CreditNoteGenerator extends AbstractInvoiceGenerator
{
    /**
     * {@inheritdoc}
     * @throws XmlGenerationException
     */
    public function generateXml(): string
    {
        if (!$this->invoice->hasReferenceNote()) {
            throw new XmlGenerationException(
                'Las notas de crédito/débito requieren la referencia al documento origen '
                . '(complemento ReferenciasNota, obligatorio para SAT); '
                . 'usa Invoice::setReferenceNote() con un FelReferenceNote.'
            );
        }

        return parent::generateXml();
    }
}
