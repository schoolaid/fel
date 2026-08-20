<?php

namespace Schoolaid\Fel\Xml\Elements;

use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelReferenceNote;
use Schoolaid\Fel\Xml\Builder\XmlDocumentBuilder;
use Schoolaid\Fel\Xml\Contracts\XmlSerializable;
use Schoolaid\Fel\Xml\Enums\ReferenceNoteXmlTags;

class ComplementsElement implements XmlSerializable
{
    public const CNO_NAMESPACE = 'http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0';

    protected XmlDocumentBuilder $builder;
    protected FelReferenceNote $referenceNote;
    protected string $documentType;

    /**
     * El catálogo de complementos de la SAT (sección 3.1) marca
     * ReferenciasNota como requerido (código 2) para NCRE y NDEB y prohibido
     * (código 0) para el resto de tipos: una FACT con el complemento se
     * rechaza con "El complemento [ReferenciasNota] con prefijo [cno] no es
     * valido para el tipo de documento [FACT]. (31101)".
     */
    public static function appliesTo(string $documentType): bool
    {
        return in_array(
            $documentType,
            [DocumentTypeEnum::CREDIT_NOTE->value, DocumentTypeEnum::DEBIT_NOTE->value],
            true
        );
    }

    public function __construct(FelReferenceNote $referenceNote, string $documentType)
    {
        $this->builder = new XmlDocumentBuilder();
        $this->referenceNote = $referenceNote;
        $this->documentType = $documentType;
    }

    /**
     * @throws XmlGenerationException
     */
    public function asXML(): string
    {
        $reference = $this->builder->buildElement(
            ReferenceNoteXmlTags::ReferenceNote->value,
            $this->getReferenceAttributes()
        );

        $complement = $this->builder->buildElement(
            ReferenceNoteXmlTags::Complement->value,
            [
                ReferenceNoteXmlTags::ComplementID->value => '1',
                ReferenceNoteXmlTags::ComplementName->value => $this->getComplementName(),
                ReferenceNoteXmlTags::ComplementURI->value => self::CNO_NAMESPACE,
            ],
            [$reference]
        );

        return $this->builder->buildElement($this->getXmlTagName(), [], [$complement]);
    }

    protected function getReferenceAttributes(): array
    {
        $note = $this->referenceNote;

        $attributes = [
            ReferenceNoteXmlTags::Namespace->value => self::CNO_NAMESPACE,
            ReferenceNoteXmlTags::Version->value => $note->version,
        ];

        if ($note->oldRegime) {
            $attributes[ReferenceNoteXmlTags::OldRegime->value] = FelReferenceNote::OLD_REGIME_VALUE;
        }

        $attributes[ReferenceNoteXmlTags::AuthorizationNumber->value] = $note->originDocumentAuthorizationNumber;
        $attributes[ReferenceNoteXmlTags::OriginSeries->value] = $note->originDocumentSeries;
        $attributes[ReferenceNoteXmlTags::OriginNumber->value] = $note->originDocumentNumber;
        $attributes[ReferenceNoteXmlTags::OriginEmissionDate->value] = $note->originDocumentEmissionDate;
        $attributes[ReferenceNoteXmlTags::AdjustmentReason->value] = $note->adjustmentReason;

        return $attributes;
    }

    protected function getComplementName(): string
    {
        return $this->documentType === DocumentTypeEnum::DEBIT_NOTE->value
            ? 'NOTA DEBITO'
            : 'NOTA CREDITO';
    }

    public function getXmlTagName(): string
    {
        return ReferenceNoteXmlTags::Complements->value;
    }
}
