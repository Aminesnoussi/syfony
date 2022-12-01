<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Avis = null;

    #[ORM\ManyToOne(inversedBy: 'Avis')]
    private ?Annonces $annonces = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvis(): ?string
    {
        return $this->Avis;
    }

    public function setAvis(string $avis): self
    {
        $this->Avis = $avis;

        return $this;
    }

    public function getAnnonces(): ?Annonces
    {
        return $this->annonces;
    }

    public function setAnnonces(?Annonces $annonces): self
    {
        $this->annonces = $annonces;

        return $this;
    }
}
