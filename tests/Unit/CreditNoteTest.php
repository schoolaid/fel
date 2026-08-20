<?php

namespace Tests\Unit;

use Schoolaid\Fel\Actions\FelGenerate;
use Schoolaid\Fel\Enums\CurrencyEnum;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
use Schoolaid\Fel\Exceptions\XmlGenerationException;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrase;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelReferenceNote;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Models\Invoice;

const CNO_NAMESPACE = 'http://www.sat.gob.gt/face2/ComplementoReferenciaNota/0.1.0';

function makeNoteInvoice(DocumentTypeEnum $type = DocumentTypeEnum::CREDIT_NOTE): Invoice
{
    $issuerAddress = new FelAddress(
        '15 AVENIDA 5-50 ZONA 15',
        '01001',
        'GUATEMALA',
        'GUATEMALA',
        'GT'
    );

    $issuer = new FelIssuer(
        'e@gmai.com',
        '1',
        '73023094',
        'DANCE STUDIO',
        IVAAffiliationTypeEnum::General,
        'DANCE STUDIO, SOCIEDAD ANONIMA',
        $issuerAddress
    );

    $receiverAddress = new FelAddress('Villa Nueva', '01064', 'GUATEMALA', 'GUATEMALA', 'GT');
    $receiver = new FelReceiver('CF', 'Consumidor Final', 'es.evitra@gmail.com', $receiverAddress);

    $phrases = new FelPhrases([new FelPhrase('1', '1')]);
    $items = new FelItems([
        new FelItem(1, 'S', 100.0, 'UND', 'Devolución 1 disciplina', 100, 1, 0, [], 100),
    ]);
    $totals = new FelTotals(grandTotal: 100);

    return new Invoice(
        $type,
        '2025-04-10T20:19:55-06:00',
        CurrencyEnum::QUETZAL,
        $issuer,
        $receiver,
        $phrases,
        $items,
        $totals
    );
}

function makeReferenceNote(): FelReferenceNote
{
    // Serie y número son obligatorios en el XSD del complemento; para un DTE
    // del régimen FEL vienen en la respuesta de certificación del origen.
    return new FelReferenceNote(
        '40A3AC05-4143-4468-B833-FA4D216AC731',
        '2020-02-26',
        'DESCUENTO',
        '40A3AC05',
        '1094466610'
    );
}

it('generates the ReferenciasNota complement for a credit note', function () {
    $invoice = makeNoteInvoice();
    $invoice->setReferenceNote(makeReferenceNote());

    $xml = FelGenerate::make($invoice)->generateXml();

    expect($xml)->toContain('Tipo="NCRE"')
        ->and($xml)->toContain('<dte:Complementos>')
        ->and($xml)->toContain(
            '<dte:Complemento IDComplemento="1" NombreComplemento="NOTA CREDITO" URIComplemento="' . CNO_NAMESPACE . '">'
        )
        ->and($xml)->toContain(
            '<cno:ReferenciasNota xmlns:cno="' . CNO_NAMESPACE . '" Version="1"'
            . ' NumeroAutorizacionDocumentoOrigen="40A3AC05-4143-4468-B833-FA4D216AC731"'
            . ' SerieDocumentoOrigen="40A3AC05" NumeroDocumentoOrigen="1094466610"'
            . ' FechaEmisionDocumentoOrigen="2020-02-26" MotivoAjuste="DESCUENTO"/>'
        );

    // El nodo Complementos debe ir después de dte:Totales dentro de DatosEmision
    expect(strpos($xml, '<dte:Complementos>'))
        ->toBeGreaterThan(strpos($xml, '</dte:Totales>'));

    // XML bien formado y con el namespace cno resoluble
    $dom = new \DOMDocument();
    expect($dom->loadXML($xml))->toBeTrue()
        ->and($dom->getElementsByTagNameNS(CNO_NAMESPACE, 'ReferenciasNota')->length)->toBe(1);
});

it('throws when a credit note has no reference to the original document', function () {
    $invoice = makeNoteInvoice();

    FelGenerate::make($invoice)->generateXml();
})->throws(XmlGenerationException::class);

it('generates the complement for a debit note with its own name', function () {
    $invoice = makeNoteInvoice(DocumentTypeEnum::DEBIT_NOTE);
    $invoice->setReferenceNote(makeReferenceNote());

    $xml = FelGenerate::make($invoice)->generateXml();

    expect($xml)->toContain('Tipo="NDEB"')
        ->and($xml)->toContain('NombreComplemento="NOTA DEBITO"')
        ->and($xml)->toContain('<cno:ReferenciasNota');
});

it('includes RegimenAntiguo only for old regime documents', function () {
    $invoice = makeNoteInvoice();
    $invoice->setReferenceNote(new FelReferenceNote(
        '1364585227',
        '2018-05-20',
        'ANULACION PARCIAL',
        '5AAE0F7A',
        '1364585227',
        true
    ));

    $xml = FelGenerate::make($invoice)->generateXml();

    expect($xml)->toContain('RegimenAntiguo="Antiguo"')
        ->and($xml)->toContain('SerieDocumentoOrigen="5AAE0F7A"')
        ->and($xml)->toContain('NumeroDocumentoOrigen="1364585227"');

    // En la nota estándar (régimen FEL) no debe aparecer la marca,
    // pero serie y número sí (los exige el XSD del complemento)
    $standard = makeNoteInvoice();
    $standard->setReferenceNote(makeReferenceNote());
    $standardXml = FelGenerate::make($standard)->generateXml();

    expect($standardXml)->not->toContain('RegimenAntiguo')
        ->and($standardXml)->toContain('SerieDocumentoOrigen="40A3AC05"')
        ->and($standardXml)->toContain('NumeroDocumentoOrigen="1094466610"');
});

it('escapes special characters in MotivoAjuste', function () {
    $invoice = makeNoteInvoice();
    $invoice->setReferenceNote(new FelReferenceNote(
        '40A3AC05-4143-4468-B833-FA4D216AC731',
        '2020-02-26',
        'Devolución <parcial> & ajuste',
        '40A3AC05',
        '1094466610'
    ));

    $xml = FelGenerate::make($invoice)->generateXml();

    // xmlwriter escapa <, > y & y codifica no-ASCII como referencia numérica
    expect($xml)->toContain('MotivoAjuste="Devoluci&#xF3;n &lt;parcial&gt; &amp; ajuste"');

    $dom = new \DOMDocument();
    expect($dom->loadXML($xml))->toBeTrue();

    $node = $dom->getElementsByTagNameNS(CNO_NAMESPACE, 'ReferenciasNota')->item(0);
    expect($node->getAttribute('MotivoAjuste'))->toBe('Devolución <parcial> & ajuste');
});

it('requires serie and numero of the origin document', function () {
    // El XSD del complemento ReferenciasNota los marca como requeridos
    // (verificado contra el sandbox de INFILE); sin ellos el DTE se rechaza.
    new FelReferenceNote(
        '40A3AC05-4143-4468-B833-FA4D216AC731',
        '2020-02-26',
        'DESCUENTO'
    );
})->throws(\ArgumentCountError::class);

it('does not emit Complementos for a regular invoice', function () {
    $invoice = makeNoteInvoice(DocumentTypeEnum::LOCAL_INVOICE);

    $xml = FelGenerate::make($invoice)->generateXml();

    expect($xml)->toContain('Tipo="FACT"')
        ->and($xml)->not->toContain('<dte:Complementos>');
});

it('rejects a reference note on a document type that must not carry the complement', function () {
    // Catálogo de complementos (3.1): ReferenciasNota es código 0 (prohibido)
    // para todo lo que no sea NCRE/NDEB. Verificado contra el sandbox de
    // INFILE: una FACT con el complemento se rechaza con
    // "El complemento [ReferenciasNota] con prefijo [cno] no es valido para
    // el tipo de documento [FACT]. (31101)". Debe fallar antes de la red.
    $invoice = makeNoteInvoice(DocumentTypeEnum::LOCAL_INVOICE);
    $invoice->setReferenceNote(makeReferenceNote());

    FelGenerate::make($invoice)->generateXml();
})->throws(XmlGenerationException::class);
