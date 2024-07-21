<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $animal_id = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['animal_read','habitat_read','race_read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['animal_read','habitat_read','race_read'])]
    private ?string $etat = null;


    #[ORM\ManyToOne(targetEntity:Habitat::class,inversedBy: 'animals',cascade: ['persist'])]
    #[Groups(groups: ['animal_read','race_read'])]
    private ?Habitat $habitat = null;



    #[ORM\ManyToOne(targetEntity:Race::class,inversedBy: 'animals',cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false,referencedColumnName: "race_id")]
    #[Groups(groups: ['animal_read','habitat_read'])]
    private ?Race $race = null;

    public function __construct()
    {

    }


    public function getAnimalId(): ?int
    {
        return $this->animal_id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }


    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }
}
