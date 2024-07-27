<?php

namespace App\Tests\unit;

use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{

    public function testAffectionRoleUserisnotnull():void{
        $user = new Utilisateur();
        $this->assertNotNull($user->getRoles());
    }

    public function testAffectionRoleUser():void{
        $user = new Utilisateur();
        $this->assertContains('ROLE_USER',$user->getRoles());
    }
    public function testAffectionNameUser():void{
        $user = new Utilisateur();
        $user->setNom("test1");
        $this->assertEquals('test1',$user->getNom());
    }
}