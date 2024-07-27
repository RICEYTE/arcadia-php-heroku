<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestRole extends WebTestCase
{
    public function testGetAllRoleKO(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/role');
        $this->assertResponseStatusCodeSame(301);
    }
    public function testGetAllRoleOK(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/role/');
        $this->assertResponseIsSuccessful();
    }
}
