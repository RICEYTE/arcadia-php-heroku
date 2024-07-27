<?php

namespace App\Tests\functional;

use http\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestHabitat extends WebTestCase
{
    public function testGetAllHabitatKo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/habitat');
        $this->assertResponseStatusCodeSame(301);
    }

    public function testGetAllHabitatOk(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/habitat/');
        $this->assertResponseIsSuccessful();
    }

    public function testGetOneHabitatInexistant(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/habitat/Ibiza');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPostOneHabitatOk(): void
    {
        //given
        $url = '/api/habitat/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestPhpUnit','description'=>'Test');



        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            json_encode($content)
        );

        $this->assertResponseIsSuccessful();

    }

    public function testPostOneHabitatAndSearchOk(): void
    {
        //given
        $url = '/api/habitat/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestPhpUnit','description'=>'Test');



        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            json_encode($content)
        );

        $client->request('GET', '/api/habitat/TestPhpUnit');
        $this->assertResponseIsSuccessful();

    }
    public function testPostHabitatEnDoublon(): void
    {
        //given
        $url = '/api/habitat/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestPhpUnit','description'=>'Test');



        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            json_encode($content)
        );


        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            json_encode($content)
        );
        $this->assertResponseStatusCodeSame(409);

    }
    public function testDeleteOneHabitatKo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('DELETE', '/api/habitat/HabitatToDelete');
        $this->assertResponseStatusCodeSame(404);
    }
    public function testPostOneHabitatAndSearchAndDeleteOk(): void
    {
        //given
        $url = '/api/habitat/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestPhpUnit','description'=>'Test');



        $client = static::createClient();
        $client->request(
            'POST',
            $url,
            array(),
            array(),
            $content_type,
            json_encode($content)
        );

        $client->request('GET', '/api/habitat/TestPhpUnit');
        $client->request('DELETE', '/api/habitat/TestPhpUnit');
        $client->request('GET', '/api/habitat/TestPhpUnit');

        $this->assertResponseStatusCodeSame(404);

    }

    public function testEditOneHabitatKo(): void
    {
        $client = static::createClient();
        $url = '/api/habitat/TestPhpUnit';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestPhpUnit','description'=>'TestModif');

        $client->request(
            'PUT',
            $url,
            array(),
            array(),
            $content_type,
            json_encode($content)
        );
        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditOneHabitatOk(): void
    {
        $client = static::createClient();
        $url = '/api/habitat/';
        $content_type = array('CONTENT_TYPE' => 'application/json');
        $content = array('nom'=>'TestPhpUnit','description'=>'Test');

        $client->request('POST', $url, array(), array(), $content_type, json_encode($content));

        $url = '/api/habitat/TestPhpUnit';
        $content = array('nom'=>'TestPhpUnit','description'=>'TestModif');
        $client->request('PUT', $url, array(), array(), $content_type, json_encode($content));

        $this->assertResponseIsSuccessful();
    }
}
