<?php

namespace App\Entity;

use App\Repository\RapportVeterinaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RapportVeterinaireRepository::class)]
class RapportVeterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $rapport_veterinaire_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(groups: ['veterinaire_read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['veterinaire_read'])]
    private ?string $detail = null;


    public function getRapportVeterinaireId(): ?int
    {
        return $this->rapport_veterinaire_id;
    }

    public function setRapportVeterinaireId(int $rapport_veterinaire_id): static
    {
        $this->rapport_veterinaire_id = $rapport_veterinaire_id;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }
}
