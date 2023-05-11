<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ListCommandeLivreurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListCommandeLivreurRepository::class)]

class ListCommandeLivreur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "date")]
    private $dateCreated;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "listCommandeLivreurs")]
    private $commande;

    #[ORM\ManyToOne(targetEntity: UserPlateform::class, inversedBy: "listCommandeLivreurs")]
    private $livreur;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getLivreur(): ?UserPlateform
    {
        return $this->livreur;
    }

    public function setLivreur(?UserPlateform $livreur): self
    {
        $this->livreur = $livreur;

        return $this;
    }
}
