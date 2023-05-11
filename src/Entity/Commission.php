<?php

namespace App\Entity;

use App\Repository\CommissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommissionRepository::class)]
class Commission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "float", nullable: true)]
    private $pourcentageProduit;

    #[ORM\Column(type: "float")]
    private $fraisLivraisonProduit;

    #[ORM\Column(type: "float")]
    private $fraisBuyLivreur;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPourcentageProduit(): ?float
    {
        return $this->pourcentageProduit;
    }

    public function setPourcentageProduit(?float $pourcentageProduit): self
    {
        $this->pourcentageProduit = $pourcentageProduit;

        return $this;
    }

    public function getFraisLivraisonProduit(): ?float
    {
        return $this->fraisLivraisonProduit;
    }

    public function setFraisLivraisonProduit(float $fraisLivraisonProduit): self
    {
        $this->fraisLivraisonProduit = $fraisLivraisonProduit;

        return $this;
    }

    public function getFraisBuyLivreur(): ?float
    {
        return $this->fraisBuyLivreur;
    }

    public function setFraisBuyLivreur(float $fraisBuyLivreur): self
    {
        $this->fraisBuyLivreur = $fraisBuyLivreur;

        return $this;
    }
}
