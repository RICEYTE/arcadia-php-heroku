<?php

namespace App\Tests\entity;

use App\Entity\Animal;
use App\Entity\Habitat;
use PHPUnit\Framework\TestCase;

class AnimalTest extends TestCase
{
    public function testAnimalCreation()
    {
        $animal = new Animal();
        $habitat = new Habitat();
        $habitat->setNom("Savane");
        $animal->setPrenom("Leo");
        $animal->setHabitat($habitat);
        $this->assertEquals('Leo',$animal->getPrenom());
        $this->assertEquals("Savane",$animal->getHabitat()->getNom());


    }
}