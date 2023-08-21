<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
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
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: "string", length: 255)]
    private $titre;

    /**
     * @ORM\Column(type="string", length=10000, nullable=true)
     */
    #[ORM\Column(type: "string", length: 10000, nullable: true)]
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    #[ORM\Column(type: "date")]
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="produits")
     */
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "produits")]
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=ListProduitPromotion::class, mappedBy="produit")
     */
    #[ORM\OneToMany(targetEntity: ListProduitPromotion::class, mappedBy: "produit")]
    private $listProduitPromotions;

    /**
     * @ORM\OneToMany(targetEntity=ProduitObject::class, mappedBy="produit")
     */
    #[ORM\OneToMany(targetEntity: ProduitObject::class, mappedBy: "produit")]
    private $produitObjects;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: "integer")]
    private $prixUnitaire;

    /**
     * @ORM\Column(type="integer")
     */
    #[ORM\Column(type: "integer")]
    private $quantite = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(type: "boolean")]
    private $status = true;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column(type: "string", length: 255)]
    private $codeProduit;

    /**
     * @ORM\ManyToOne(targetEntity=Boutique::class, inversedBy="produits")
     */
    #[ORM\ManyToOne(targetEntity: Boutique::class, inversedBy: "produits")]
    private $boutique;

    /**
     * @ORM\OneToMany(targetEntity=ListProduitPanier::class, mappedBy="produit")
     */
    #[ORM\OneToMany(targetEntity: ListProduitPanier::class, mappedBy: "produit")]
    private $listProduitPaniers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(type: "integer", nullable: true)]
    private $taille;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: NegociationProduit::class)]
    private Collection $negociationProduits;

    #[ORM\Column(nullable: true)]
    private ?bool $negociable = false;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ListProduitShort::class)]
    private Collection $listProduitShorts;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->negociable = false;
        $this->listProduitPromotions = new ArrayCollection();
        $this->produitObjects = new ArrayCollection();
        $this->listProduitPaniers = new ArrayCollection();
        $this->negociationProduits = new ArrayCollection();
        $this->listProduitShorts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ListProduitPromotion>
     */
    public function getListProduitPromotions(): Collection
    {
        return $this->listProduitPromotions;
    }

    public function addListProduitPromotion(ListProduitPromotion $listProduitPromotion): self
    {
        if (!$this->listProduitPromotions->contains($listProduitPromotion)) {
            $this->listProduitPromotions[] = $listProduitPromotion;
            $listProduitPromotion->setProduit($this);
        }

        return $this;
    }

    public function removeListProduitPromotion(ListProduitPromotion $listProduitPromotion): self
    {
        if ($this->listProduitPromotions->removeElement($listProduitPromotion)) {
            // set the owning side to null (unless already changed)
            if ($listProduitPromotion->getProduit() === $this) {
                $listProduitPromotion->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProduitObject>
     */
    public function getProduitObjects(): Collection
    {
        return $this->produitObjects;
    }

    public function addProduitObject(ProduitObject $produitObject): self
    {
        if (!$this->produitObjects->contains($produitObject)) {
            $this->produitObjects[] = $produitObject;
            $produitObject->setProduit($this);
        }

        return $this;
    }

    public function removeProduitObject(ProduitObject $produitObject): self
    {
        if ($this->produitObjects->removeElement($produitObject)) {
            // set the owning side to null (unless already changed)
            if ($produitObject->getProduit() === $this) {
                $produitObject->setProduit(null);
            }
        }

        return $this;
    }

    public function getPrixUnitaire(): ?int
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(int $prixUnitaire): self
    {
        $this->prixUnitaire = $prixUnitaire;

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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCodeProduit(): ?string
    {
        return $this->codeProduit;
    }

    public function setCodeProduit(string $codeProduit): self
    {
        $this->codeProduit = $codeProduit;

        return $this;
    }

    public function getBoutique(): ?Boutique
    {
        return $this->boutique;
    }

    public function setBoutique(?Boutique $boutique): self
    {
        $this->boutique = $boutique;

        return $this;
    }

    /**
     * @return Collection<int, ListProduitPanier>
     */
    public function getListProduitPaniers(): Collection
    {
        return $this->listProduitPaniers;
    }

    public function addListProduitPanier(ListProduitPanier $listProduitPanier): self
    {
        if (!$this->listProduitPaniers->contains($listProduitPanier)) {
            $this->listProduitPaniers[] = $listProduitPanier;
            $listProduitPanier->setProduit($this);
        }

        return $this;
    }

    public function removeListProduitPanier(ListProduitPanier $listProduitPanier): self
    {
        if ($this->listProduitPaniers->removeElement($listProduitPanier)) {
            // set the owning side to null (unless already changed)
            if ($listProduitPanier->getProduit() === $this) {
                $listProduitPanier->setProduit(null);
            }
        }

        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(int $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * @return Collection<int, NegociationProduit>
     */
    public function getNegociationProduits(): Collection
    {
        return $this->negociationProduits;
    }

    public function addNegociationProduit(NegociationProduit $negociationProduit): self
    {
        if (!$this->negociationProduits->contains($negociationProduit)) {
            $this->negociationProduits->add($negociationProduit);
            $negociationProduit->setProduit($this);
        }

        return $this;
    }

    public function removeNegociationProduit(NegociationProduit $negociationProduit): self
    {
        if ($this->negociationProduits->removeElement($negociationProduit)) {
            // set the owning side to null (unless already changed)
            if ($negociationProduit->getProduit() === $this) {
                $negociationProduit->setProduit(null);
            }
        }

        return $this;
    }

    public function isNegociable(): ?bool
    {
        return $this->negociable;
    }

    public function setNegociable(?bool $negociable): self
    {
        $this->negociable = $negociable;

        return $this;
    }

    /**
     * @return Collection<int, ListProduitShort>
     */
    public function getListProduitShorts(): Collection
    {
        return $this->listProduitShorts;
    }

    public function addListProduitShorts(ListProduitShort $listProduitShorts): static
    {
        if (!$this->listProduitShorts->contains($listProduitShorts)) {
            $this->listProduitShorts->add($listProduitShorts);
            $listProduitShorts->setProduit($this);
        }

        return $this;
    }

    public function removeListProduitShorts(ListProduitShort $listProduitShorts): static
    {
        if ($this->listProduitShorts->removeElement($listProduitShorts)) {
            // set the owning side to null (unless already changed)
            if ($listProduitShorts->getProduit() === $this) {
                $listProduitShorts->setProduit(null);
            }
        }

        return $this;
    }
}
