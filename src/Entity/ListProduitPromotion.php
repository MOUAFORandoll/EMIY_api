<?php

namespace App\Entity;

use App\Repository\ListProduitPromotionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListProduitPromotionRepository::class)]
class ListProduitPromotion
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;


    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: "listProduitPromotions")]
    private $produit;


    #[ORM\ManyToOne(targetEntity: Promotion::class, inversedBy: "listProduitPromotions")]
    private $promotion;


    #[ORM\Column(type: "date")]
    private $dateCreated;


    #[ORM\Column(type: "integer")]
    private $prixPromotion;


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

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;

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

    public function getPrixPromotion(): ?int
    {
        return $this->prixPromotion;
    }

    public function setPrixPromotion(int $prixPromotion): self
    {
        $this->prixPromotion = $prixPromotion;

        return $this;
    }
}
