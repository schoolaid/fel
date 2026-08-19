<?php

namespace Tests\Unit;

use GuzzleHttp\Exception\GuzzleException;
use Schoolaid\Fel\Certification\Exceptions\CertificationException;
use Schoolaid\Fel\Certification\FelCertificationService;
use Schoolaid\Fel\Config\FelConfig;

it('wraps provider cancellation failures preserving the original exception', function () {
    // Puerto 1 en loopback: la conexión se rechaza al instante y sin red,
    // provocando la ruta de error real del provider.
    $config = new FelConfig('infile', 'usuario_demo', 'LLAVE_A', 'LLAVE_B', [
        'base_url' => 'http://127.0.0.1:1/',
        'cancel_url' => 'anulacion',
    ]);

    $service = new FelCertificationService($config);

    try {
        $service->cancel('<dte:GTAnulacionDocumento/>');
        $this->fail('Se esperaba CertificationException');
    } catch (CertificationException $e) {
        expect($e->getMessage())->toContain('Cancellation failed')
            ->and($e->getPrevious())->toBeInstanceOf(GuzzleException::class);
    }
});
