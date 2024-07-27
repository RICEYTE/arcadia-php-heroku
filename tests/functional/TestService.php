<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestService extends WebTestCase
{
    public function testGetAllServiceKO(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/service');
        $this->assertResponseStatusCodeSame(301);
    }
    public function testGetAllServiceOK(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/service/');
        $this->assertResponseIsSuccessful();
    }
}
