<?php

namespace AcquiaCloudApi\Tests\Endpoints;

use AcquiaCloudApi\Tests\CloudApiTestCase;
use AcquiaCloudApi\Connector\Client;
use AcquiaCloudApi\Connector\Connector;
use AcquiaCloudApi\Endpoints\Code;

class ClientTest extends CloudApiTestCase
{

    public function testAddQuery()
    {
        $client = $this->getMockClient();

        $client->addQuery('filter', 'name=dev');
        $client->addQuery('filter', 'type=file');

        $expectedQuery = [
            'filter' => [
                'name=dev',
                'type=file',
            ],
        ];

        $this->assertEquals($expectedQuery, $client->getQuery());
    }

    public function testFilteredQuery()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/Client/getFilteredCode.json');
        $client = $this->getMockClient($response);

        /** @var \AcquiaCloudApi\CloudApi\ClientInterface $client */
        $client->addQuery('filter', 'name=@*2014*');
        $client->addQuery('filter', 'type=@*true*');
        $code = new Code($client);
        $result = $code->getAll('8ff6c046-ec64-4ce4-bea6-27845ec18600');

        foreach ($result as $record) {
            $this->assertContains('2014', $record->name);
        }
    }

    public function testClearQuery()
    {
        $client = $this->getMockClient();

        $client->addQuery('filter', 'name=dev');
        $this->assertEquals(['filter' => 'name=dev'], $client->getQuery());

        $client->clearQuery();
        $this->assertTrue(empty($client->getQuery()));
    }

    public function testOptions()
    {
        $client = $this->getMockClient();

        $client->addOption('verify', 'false');
        $client->addOption('curl.options', ['CURLOPT_RETURNTRANSFER' => true]);
        $client->addOption('curl.options', ['CURLOPT_FILE' => '/tmp/foo']);

        $expectedOptions = [
            'verify' => 'false',
            'curl.options' => [
                'CURLOPT_RETURNTRANSFER' => true,
                'CURLOPT_FILE' => '/tmp/foo',
            ],
        ];

        $this->assertEquals($expectedOptions, $client->getOptions());

        $client->clearOptions();
        $this->assertTrue(empty($client->getOptions()));
    }
}
