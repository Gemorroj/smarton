<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/order/list');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['status']);
    }


    /**
     * @dataProvider successCreateProvider
     */
    public function testSuccessCreate($requestParameters)
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/order/create', $requestParameters);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['status']);
    }

    public function successCreateProvider()
    {
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12,3',
            'isLegalPerson' => 'true',
            'attributes' => '{"test": 123, "someattr": "somevalue"}',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12.3', // подходит как запятая, так и точка
            'isLegalPerson' => 'false',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12.3',
            'isLegalPerson' => 'false',
            // атррибуыы не обязательны
        ]];
    }

    /**
     * @dataProvider failCreateProvider
     */
    public function testFailCreate($requestParameters)
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/order/create', $requestParameters);

        $this->assertSame(422, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $client->getResponse()->headers->get('content-type'));
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($data['status']);
    }

    public function failCreateProvider()
    {
        yield [[
            'facebookId' => 'str', // нужны числа
            'currency' => 'BYN',
            'totalCost' => '12,3',
            'isLegalPerson' => 'false',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'ABC', // некорректный код валюты
            'totalCost' => '12,3',
            'isLegalPerson' => 'false',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12,377', // слишком большая дробная часть
            'isLegalPerson' => 'true',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12345678912,37', // слишком большая целая часть
            'isLegalPerson' => 'true',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12,3',
            'isLegalPerson' => 'true',
            'attributes' => 'test{"test": 123, "someattr": "somevalue"}', // некорректный json
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '12,3',
            'isLegalPerson' => 'true',
            'attributes' => 'null', // нужен массив/объект json
        ]];
        yield [[
            'facebookId' => '', // обязателен
            'currency' => 'BYN',
            'totalCost' => '12,3',
            'isLegalPerson' => 'true',
            'attributes' => '{"test": 123, "someattr": "somevalue"}',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => '', // обязателен
            'totalCost' => '12,3',
            'isLegalPerson' => 'true',
            'attributes' => '{"test": 123, "someattr": "somevalue"}',
        ]];
        yield [[
            'facebookId' => '123',
            'currency' => 'BYN',
            'totalCost' => '', // обязателен
            'isLegalPerson' => 'true',
            'attributes' => '{"test": 123, "someattr": "somevalue"}',
        ]];
    }
}
