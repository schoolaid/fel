<?php
namespace SchoolAid\FEL\Enum;

enum CancelBillXML: string {
    case Tag                  = 'dte:DatosGenerales';
    case IdAttribute          = 'ID';
    case Id                   = 'DatosAnulacion';
    case DateCancel           = 'FechaHoraAnulacion';
    case IdIssuer             = 'NITEmisor';
    case DateDocumentToCancel = 'FechaEmisionDocumentoAnular';
    case IdCustomer           = 'IDReceptor';
    case DocumentNumber       = 'NumeroDocumentoAAnular';
    case Reason               = 'MotivoAnulacion';
}
