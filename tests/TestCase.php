<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Schoolaid\Fel\FelServiceProvider;

abstract class TestCase extends BaseTestCase
{
    //
    protected function setUp(): void
    {
        parent::setUp();
    }
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getEnvironmentSetUp($app): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $config = $app->get('config');
        $config->set('fel.provider', env('FEL_PROVIDER', ''));
        $config->set('fel.username', env('FEL_USERNAME', ''));
        $config->set('fel.api_key', env('FEL_API_KEY', ''));
        $config->set('fel.signature_key', env('FEL_SIGNATURE_KEY', ''));
        $config->set('fel.provider_config.base_url', env('FEL_BASE_URL', ''));
        $config->set('fel.provider_config.certify_url', env('FEL_CERTIFY_URL', ''));
        $config->set('fel.provider_config.status_url', env('FEL_STATUS_URL', ''));
        $config->set('fel.provider_config.cancel_url', env('FEL_CANCEL_URL', ''));
        $config->set('fel.provider_config.timeout', env('FEL_TIMEOUT', 30));
        $config->set('fel.provider_config.verify_ssl', env('FEL_VERIFY_SSL', true));
    }

    protected function getPackageProviders($app): array
    {
        return [
            FelServiceProvider::class
        ];
    }
}