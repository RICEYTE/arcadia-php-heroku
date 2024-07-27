<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestLogin extends WebTestCase
{

    public function testLoginKO(): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/login');

       // $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(401);

    }
}
