<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $service_id = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['service_read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['service_read'])]
    private ?string $description = null;

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
