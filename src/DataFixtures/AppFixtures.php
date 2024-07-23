<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Avis;
use App\Entity\Habitat;
use App\Entity\Race;
use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);


        $roles_liste = ["ROLE_ADMIN","ROLE_EMPLOYE","ROLE_VETERINAIRE"];
        $roles = new ArrayCollection();

        foreach ($roles_liste as $role_name){
            $role = new Role();
            $role->setLabel($role_name);
            $manager->persist($role);
            $roles->add($role);
        }

        for($indexUtilisateur=0;$indexUtilisateur<10;$indexUtilisateur++){

            for($indexUtilisateurParRole = 0;$indexUtilisateurParRole<3;$indexUtilisateurParRole++ ){
                $user = new Utilisateur();
                $user->setNom($faker->name);
                $user->setCreatedAt(new \DateTimeImmutable());
                $user->setPassword($faker->words(1, true));
                $user->setRoles([$roles[$indexUtilisateur]]);
                $user->setPrenom($faker->firstName);
                $user->setUsername($faker->email);
                $manager->persist($user);
            }
        }

        $habitat_name_list = ["Savane","Marais","Jungle"];

        $habitatsList = new ArrayCollection();
        foreach ($habitat_name_list as $habitat_name) {

            $habitat = new Habitat();
            $habitat->setNom($habitat_name);
            $habitat->setDescription($faker->words(5, true));
            $manager->persist($habitat);
            $habitatsList->add($habitat);
        }

        $races_savane = ["Lion","guépar","girafe","Eléphant"];
        $races_marais = ["Crocodile","Aligator"];
        $races_jungle = ["Singe","gorille","cobra"];

        $racesList = [$races_savane,$races_marais,$races_jungle];

        $indexHabitat = 0;

        foreach ($racesList as $races) {


            foreach ($races as $race_name) {
                $race = new Race();
                $race->setLabel($race_name);
                $manager->persist($race);

                for ($i = 0; $i < 10; $i++) {
                    $animal = new Animal();
                    $animal->setPrenom($faker->firstName);
                    $animal->setEtat("OK");
                    $animal->setRace($race);
                    $animal->setHabitat($habitatsList[$indexHabitat]);
                    $manager->persist($animal);
                }

            }
            $indexHabitat++;
        }

        for($indexAvis=0; $indexAvis<10;$indexAvis++){
            $avis = new Avis();
            $avis->setPseudo($faker->firstName);
            $avis->setCreatedAt(new \DateTimeImmutable());
            $avis->setCommentaire("commentaire");
            $avis->setVisible(false);
            $manager->persist($avis);
        }




        $manager->flush();
    }
}
