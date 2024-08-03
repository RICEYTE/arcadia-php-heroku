<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestRace extends WebTestCase
{
    public function testGetAllRaceKo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/race');
        $this->assertResponseStatusCodeSame(301);
    }
    public function testGetAllRaceOk(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/race/');
        $this->assertResponseIsSuccessful();
    }
    public function testGetOneRaceKo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/race/raceTest');
        $this->assertResponseStatusCodeSame(404);
    }
    public function testPostOneRaceOk(): void
    {
        $client = static::createClient();
        $url = '/api/race/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('label'=>'TestRace');

        $client->request('POST', $url, array(), array(), $content_type, json_encode($content));
        $client->request('GET', '/api/race/TestRace');
        $this->assertResponseIsSuccessful();
    }
    public function testDeleteOneRaceOk(): void
    {
        $client = static::createClient();
        $url = '/api/race/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('label'=>'TestRace');

        $client->request('POST', $url, array(), array(), $content_type, json_encode($content));
        $client->request('GET', '/api/race/TestRace');
        $client->request('DELETE', '/api/race/TestRace');
        $client->request('GET', '/api/race/TestRace');
        $this->assertResponseStatusCodeSame(404);
    }
    public function testDeleteOneRaceKo(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/race/TestRace');
        $this->assertResponseStatusCodeSame(404);
    }
}
