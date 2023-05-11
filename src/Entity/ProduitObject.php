<?php

namespace App\Entity;

use App\Repository\ProduitObjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 

#[ORM\Entity(repositoryClass: ProduitObjectRepository::class)]

class ProduitObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "string", length: 100000000)]
    private $src;

    #[ORM\Column(type: "date")]
    private $dateCreated;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: "produitObjects")]
    private $produit;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;

        return $this;
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

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
