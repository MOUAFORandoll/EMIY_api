<?php
// src/Entity/LikeProduit.php

namespace App\Entity;

use App\Repository\LikeProduitRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeProduitRepository::class)]
class LikeProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "boolean")]
    private $like_produit;

    #[ORM\Column(type: "date")]

    private $dateCreated;

    #[ORM\ManyToOne(inversedBy: 'LikeProduits')]
    private ?UserPlateform $client = null;

    #[ORM\ManyToOne(inversedBy: 'LikeProduits')]
    private ?Produit $produit = null;



    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->like_produit = true;
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

    public function isLike_produit(): ?bool
    {
        return $this->like_produit;
    }

    public function setLike_produit(bool $like_produit): self
    {
        $this->like_produit = $like_produit;

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
