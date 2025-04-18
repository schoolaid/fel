<?php

namespace Schoolaid\Fel\Xml\Generators;

use DateTimeInterface;
use DOMDocument;
use DOMException;
use Schoolaid\Fel\Models\Cancellation;
use Schoolaid\Fel\Xml\Contracts\XmlGeneratorInterface;

/**
 * Generator for cancellation XML documents
 */
class CancellationGenerator implements XmlGeneratorInterface
{
    /**
     * Generate XML for cancellation
     *
     * @param Cancellation $model
     * @return string
     * @throws DOMException
     */
    public function generate($model): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        // Root element
        $cancellationDoc = $dom->createElement('dte:GTAnulacionDocumento');
        $cancellationDoc->setAttribute('Version', '0.1');
        $cancellationDoc->setAttribute('xmlns:dte', 'http://www.sat.gob.gt/dte/fel/0.1.0');
        $cancellationDoc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->appendChild($cancellationDoc);
        
        // SAT element
        $satElement = $dom->createElement('dte:SAT');
        $cancellationDoc->appendChild($satElement);
        
        // AnulacionDTE element
        $cancelElement = $dom->createElement('dte:AnulacionDTE');
        $cancelElement->setAttribute('ID', 'DatosCertificados');
        $satElement->appendChild($cancelElement);
        
        // DatosGenerales element
        $generalData = $dom->createElement('dte:DatosGenerales');
        $generalData->setAttribute('ID', 'DatosAnulacion');
        
        $cancellationDate = $model->getCancellationDateTime();
        $documentsDate = $model->getDocumentDateTime();
        
        $generalData->setAttribute('FechaHoraAnulacion', $cancellationDate);
        $generalData->setAttribute('NITEmisor', $model->getNitIssuer());
        $generalData->setAttribute('FechaEmisionDocumentoAnular', $documentsDate);
        $generalData->setAttribute('IDReceptor', $model->getIdReceiver());
        $generalData->setAttribute('NumeroDocumentoAAnular', $model->getDocumentUuid());
        $generalData->setAttribute('MotivoAnulacion', $model->getReason());
        
        $cancelElement->appendChild($generalData);
        
        return $dom->saveXML();
    }
    
    /**
     * Format date time with timezone offset for Guatemala
     *
     * @param DateTimeInterface $dateTime
     * @return string
     */
    protected function formatDateTime(DateTimeInterface $dateTime): string
    {
        return $dateTime->format('c');
    }
} 