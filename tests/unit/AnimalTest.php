<?php

namespace App\Tests\unit;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Entity\Race;
use PHPUnit\Framework\TestCase;

class AnimalTest extends TestCase
{

    public function testAnimalCreationValid()
    {

        //given
        $habitat = new Habitat();
        $habitat->setNom("Savane");
        $habitat->setDescription("La savane est grande ...");

        $race = new Race();
        $race->setLabel("Lion");

        //test
        $animal = new Animal();
        $animal->setPrenom("Leo");
        $animal->setEtat("OK");
        $animal->setHabitat($habitat);
        $animal->setRace($race);

        $this->assertSame('Leo',$animal->getPrenom());
        $this->assertSame("Savane",$animal->getHabitat()->getNom());
        $this->assertSame("La savane est grande ...",$animal->getHabitat()->getDescription());
        $this->assertSame("Lion",$animal->getRace()->getLabel());
    }
    public function testAnimalCreationInvalid()
    {

        //given
        $animal = new Animal();
        $animal->setPrenom("Leo");
        $animal->setEtat("OK");
        //test

        $this->assertSame('Leo',$animal->getPrenom());
        $this->assertNull($animal->getHabitat());
        $this->assertNull($animal->getRace());

    }
}