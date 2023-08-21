<?php

namespace App\Entity;

use App\Repository\ListProduitShortRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListProduitShortRepository::class)]
class ListProduitShort
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'listProduitShorts')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'listProduitShorts')]
    private ?Short $short = null;

    #[ORM\Column(type: "date", nullable: true)]
    private $dateCreated;
    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getShort(): ?Short
    {
        return $this->short;
    }

    public function setShort(?Short $short): static
    {
        $this->short = $short;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}
