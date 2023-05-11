<?php
// src/Entity/NotationProduit.php

namespace App\Entity;

use App\Repository\NotationProduitRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotationProduitRepository::class)]
class NotationProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "float")]
    private $note;

    #[ORM\Column(type: "date")]

    private $dateCreated;

    #[ORM\ManyToOne(inversedBy: 'notationProduits')]
    private ?UserPlateform $client = null;

    #[ORM\ManyToOne(inversedBy: 'notationProduits')]
    private ?Produit $produit = null;



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

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getClient(): ?UserPlateform
    {
        return $this->client;
    }

    public function setClient(?UserPlateform $client): self
    {
        $this->client = $client;

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
