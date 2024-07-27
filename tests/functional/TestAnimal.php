<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestAnimal extends WebTestCase
{
    public function testGetAllAnimalKo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animal');
        $this->assertResponseStatusCodeSame(301);
    }
    public function testGetOneAnimalKo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animal/Leo');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetAllAnimalOk(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/animal/');
        $this->assertResponseIsSuccessful();
    }

    public function testPostOneAnimalKo(): void
    {
        //given
        $url = '/api/animal/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = '[{"title":"title1","body":"body1"}]';

        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            $content
        );
        $this->assertResponseStatusCodeSame(400);
    }
    public function testPostOneAnimalKoErreur500(): void
    {
        //given
        $url = '/api/animal/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = '';

        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            $content
        );
        $this->assertResponseStatusCodeSame(500);
    }
    public function testPostOneAnimalKoErreurVide(): void
    {
        //given
        $url = '/api/animal/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = '[]';

        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            $content
        );
        $this->assertResponseStatusCodeSame(400);
    }
}
