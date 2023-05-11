<?php

namespace App\Entity;

use App\Repository\ListProduitPanierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListProduitPanierRepository::class)
 */
#[ORM\Entity(repositoryClass: ListProduitPanierRepository::class)]
class ListProduitPanier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Panier::class, inversedBy="listProduitPaniers")
     */
    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: "listProduitPaniers")]
    private $panier;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="listProduitPaniers")
     */
    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: "listProduitPaniers")]
    private $produit;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(type: "boolean")]
    private $status = false;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: "integer")]
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: "string", length: 255)]
    private $codeProduitPanier;

    /**
     * @ORM\Column(type="datetime")
     */
    #[ORM\Column(type: "datetime")]
    private $dateCreated;

    // Rest of your class properties and methods
    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }


    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

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
    public function getCodeProduitPanier(): ?string
    {
        return $this->codeProduitPanier;
    }

    public function setCodeProduitPanier(string $codeProduitPanier): self
    {
        $this->codeProduitPanier = $codeProduitPanier;

        return $this;
    }
}
