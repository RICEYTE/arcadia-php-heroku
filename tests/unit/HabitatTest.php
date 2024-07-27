<?php

namespace App\Tests\unit;

use App\Entity\Habitat;
use PHPUnit\Framework\TestCase;

class HabitatTest extends TestCase
{
    public function testHabitatCreation()
    {
        $habitat = new Habitat();
        $habitat->setNom("Savane");
        $habitat->setDescription("Petite description..");
        $this->assertSame('Savane',$habitat->getNom());
        $this->assertSame("Petite description..",$habitat->getDescription());
        $this->assertNull($habitat->getCommentaireHabitat());

    }
}