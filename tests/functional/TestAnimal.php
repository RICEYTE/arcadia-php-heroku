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


    public function testPostOneAnimalOk(): void
    {
        $client = static::createClient();
        $url = '/api/race/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('label'=>'TestRace');
        $client->request('POST', $url, array(), array(), $content_type, json_encode($content));

        $url = '/api/habitat/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestHabitat','description'=>'Test');
        $client->request('POST', $url, array(), array(), $content_type, json_encode($content));

        $url = '/api/animal/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('prenom'=>'TestAnimal','etat'=>'OK','race'=>'TestRace','habitat'=>'TestHabitat');
        $client->request('POST', $url, array(), array(), $content_type, json_encode($content));
        $client->request('GET', '/api/animal/TestAnimal');
        $this->assertResponseIsSuccessful();
    }

    
}
