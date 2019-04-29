<?php


use Orchestra\Testbench\TestCase;
use Dezsidog\LYouzanphp\ServiceProvider;
use \Dezsidog\LYouzanphp\Manager;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('larazan.clients', [
            'default' => [
                'clientId' => 'test1',
                'clientSecret' => 'secret1',
            ],
            'sec' => [
                'clientId' => 'test2',
                'clientSecret' => 'secret2'
            ]
        ]);
    }

    public function testMake()
    {
        /** @var Manager $sdk */
        $sdk = $this->app->make(Manager::class);
        $this->assertEquals('test1', $sdk->getClientId());
        $this->assertEquals('secret1', $sdk->getClientSecret());
    }

    public function testMakeWithAnotherClientConfig()
    {
        /** @var Manager $sdk */
        $sdk = $this->app->make(Manager::class, ['client' => 'sec']);
        $this->assertEquals('test2', $sdk->getClientId());
        $this->assertEquals('secret2', $sdk->getClientSecret());
    }
}