<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestRace extends WebTestCase
{
    public function testGetAllRaceKO(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/race');
        $this->assertResponseStatusCodeSame(301);
    }
    public function testGetAllRaceOK(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/race/');
        $this->assertResponseIsSuccessful();
    }
}
