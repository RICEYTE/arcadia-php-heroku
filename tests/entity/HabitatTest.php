<?php

namespace App\Tests\entity;

use App\Entity\Habitat;
use PHPUnit\Framework\TestCase;

class HabitatTest extends TestCase
{
    public function testHabitatCreation()
    {
        $habitat = new Habitat();
        $habitat->setNom("Savane");
        $habitat->setDescription("Petite description..");
        $this->assertEquals('Savane',$habitat->getNom());
        $this->assertEquals("Petite description..",$habitat->getDescription());
        $this->assertNull($habitat->getCommentaireHabitat());

    }
}