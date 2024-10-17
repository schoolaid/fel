<?php

namespace Tests;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function getEnvironmentSetUp($app): void
    {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $config = $app->get('config');
        $config->set('fel.UsuarioFirma', env('USUARIO_FIRMA'));
        $config->set('fel.llaveFirma', env('LLAVE_FIRMA'));
        $config->set('fel.UsuarioApi', env('USUARIO_API'));
        $config->set('fel.llaveApi', env('LLAVE_API'));
        $config->set('fel.Usuario', env('USUARIO'));
        $config->set('fel.llave', env('LLAVE'));

    }

}
