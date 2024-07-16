<?php

namespace App\Tests\controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{

    public function testApiLogin401():void
    {
        $client = self::createClient();
        $client->followRedirects(false);
        $client->request('POST', 'api/login');

        //self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(401);
    }
    public function testApiLogin1():void
    {
        $client = self::createClient();
        $client->followRedirects(false);



            $client->request('POST', 'api/login', [], [], server: ['Contet-Type' => 'application/json',],
                content:'{"username":"administrateur.arcadia@arcadia.fr","password":"admin"}');

        self::assertResponseIsSuccessful();
        //self::assertResponseStatusCodeSame(401);
    }
}