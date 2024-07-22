<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $avis_id = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['avis_read'])]
    private ?string $pseudo = null;

    #[ORM\Column(length: 50)]
    #[Groups(groups: ['avis_read'])]
    private ?string $commentaire = null;

    #[ORM\Column]
    #[Groups(groups: ['avis_read'])]
    private ?bool $isVisible = null;

    #[ORM\Column]
    #[Groups(groups: ['avis_read'])]
    private ?\DateTimeImmutable $createdAt = null;


    public function getAvisId(): ?int
    {
        return $this->avis_id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
