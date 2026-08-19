<?php

namespace Tests\Unit;

use DOMDocument;
use DOMXPath;
use Schoolaid\Fel\Actions\FelGenerate;
use Schoolaid\Fel\Enums\DocumentTypeEnum;
use Schoolaid\Fel\Enums\IVAAffiliationTypeEnum;
use Schoolaid\Fel\Models\FelAddenda;
use Schoolaid\Fel\Models\FelAddress;
use Schoolaid\Fel\Models\FelIssuer;
use Schoolaid\Fel\Models\FelItem;
use Schoolaid\Fel\Models\FelItems;
use Schoolaid\Fel\Models\FelPhrase;
use Schoolaid\Fel\Models\FelPhrases;
use Schoolaid\Fel\Models\FelReceiver;
use Schoolaid\Fel\Models\FelTotals;
use Schoolaid\Fel\Models\Invoice;

function makeXmlEscapingInvoice(string $description, string $adendaValue): Invoice
{
    $issuer = new FelIssuer(
        'facturacion@empresa.com',
        '1',
        '120035502',
        'Empresa',
        IVAAffiliationTypeEnum::General,
        'Empresa, S.A.',
        new FelAddress('Ciudad', '01010', 'Guatemala', 'Guatemala', 'GT')
    );

    return new Invoice(
        DocumentTypeEnum::LOCAL_INVOICE,
        '2026-07-11T08:09:21-06:00',
        'GTQ',
        $issuer,
        new FelReceiver('CF', 'cliente@correo.com', 'Consumidor Final', new FelAddress('Ciudad', '01010', 'Guatemala', 'Guatemala', 'GT')),
        new FelPhrases([new FelPhrase('2', '1')]),
        new FelItems([new FelItem(1, 'S', 50, 'UND', $description, 50, 1, 0, [], 50)]),
        new FelTotals(),
        [new FelAddenda('https://www.sat.gob.gt/fel/addenda', 'Orden', $adendaValue)]
    );
}

it('escapes special characters in item descriptions', function () {
    $description = 'Camisa talla <M> & accesorios';
    $invoice = makeXmlEscapingInvoice($description, 'Orden #1');

    $xml = (new FelGenerate($invoice))->generateXml();

    $dom = new DOMDocument();
    expect(@$dom->loadXML($xml))->toBeTrue();

    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('dte', 'http://www.sat.gob.gt/dte/fel/0.2.0');
    $nodes = $xpath->query('//dte:Item/dte:Descripcion');
    expect($nodes->length)->toBe(1)
        ->and($nodes->item(0)->textContent)->toBe($description);
});

it('escapes special characters in adenda values', function () {
    $adendaValue = 'Orden <urgente> "Q&A" - agosto 2026';
    $invoice = makeXmlEscapingInvoice('2026 - DAY PASS', $adendaValue);

    $xml = (new FelGenerate($invoice))->generateXml();

    $dom = new DOMDocument();
    expect(@$dom->loadXML($xml))->toBeTrue();

    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('dte', 'http://www.sat.gob.gt/dte/fel/0.2.0');
    $nodes = $xpath->query('//dte:Adenda/*[local-name()="Orden"]');
    expect($nodes->length)->toBe(1)
        ->and($nodes->item(0)->textContent)->toBe($adendaValue);
});
