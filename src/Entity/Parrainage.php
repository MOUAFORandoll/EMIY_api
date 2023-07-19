<?php

namespace App\Entity;

use App\Repository\ParrainageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParrainageRepository::class)]
class Parrainage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'parrainages')]
    private ?UserPlateform $parrain = null;

    #[ORM\ManyToOne(inversedBy: 'parrainages')]
    private ?UserPlateform $fieul = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParrain(): ?UserPlateform
    {
        return $this->parrain;
    }

    public function setParrain(?UserPlateform $parrain): static
    {
        $this->parrain = $parrain;

        return $this;
    }

    public function getFieul(): ?UserPlateform
    {
        return $this->fieul;
    }

    public function setFieul(?UserPlateform $fieul): static
    {
        $this->fieul = $fieul;

        return $this;
    }
}
