<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BoutiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoutiqueRepository::class)]

class Boutique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $titre;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: "boolean")]
    private $status = false;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $codeBoutique;

    #[ORM\Column(type: "date", nullable: true)]
    private $dateCreated;

    #[ORM\ManyToOne(targetEntity: UserPlateform::class, inversedBy: "boutiques")]
    private $user;

    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: "boutique")]
    private $produits;

    #[ORM\OneToMany(targetEntity: BoutiqueObject::class, mappedBy: "boutique")]
    private $boutiqueObjects;

    #[ORM\ManyToOne(targetEntity: Localisation::class, inversedBy: "boutiques")]

    private $localisation;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "boutiques")]
    private $category;

    #[ORM\OneToMany(targetEntity: Short::class, mappedBy: "boutique")]

    private $shorts;

    #[ORM\OneToMany(mappedBy: 'boutique', targetEntity: NotationBoutique::class)]
    private Collection $notationBoutiques;

    #[ORM\OneToMany(mappedBy: 'boutique', targetEntity: AbonnementBoutique::class)]
    private Collection $abonnementBoutiques;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->produits = new ArrayCollection();
        $this->boutiqueObjects = new ArrayCollection();
        $this->shorts = new ArrayCollection();
        $this->notationBoutiques = new ArrayCollection();
        $this->abonnementBoutiques = new ArrayCollection();
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

    public function getCodeBoutique(): ?string
    {
        return $this->codeBoutique;
    }

    public function setCodeBoutique(?string $codeBoutique): self
    {
        $this->codeBoutique = $codeBoutique;

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

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getUser(): ?UserPlateform
    {
        return $this->user;
    }

    public function setUser(?UserPlateform $user): self
    {
        $this->user = $user;

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

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setBoutique($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getBoutique() === $this) {
                $produit->setBoutique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoutiqueObject>
     */
    public function getBoutiqueObjects(): Collection
    {
        return $this->boutiqueObjects;
    }

    public function addBoutiqueObject(BoutiqueObject $boutiqueObject): self
    {
        if (!$this->boutiqueObjects->contains($boutiqueObject)) {
            $this->boutiqueObjects[] = $boutiqueObject;
            $boutiqueObject->setBoutique($this);
        }

        return $this;
    }

    public function removeBoutiqueObject(BoutiqueObject $boutiqueObject): self
    {
        if ($this->boutiqueObjects->removeElement($boutiqueObject)) {
            // set the owning side to null (unless already changed)
            if ($boutiqueObject->getBoutique() === $this) {
                $boutiqueObject->setBoutique(null);
            }
        }

        return $this;
    }

    public function getLocalisation(): ?Localisation
    {
        return $this->localisation;
    }

    public function setLocalisation(?Localisation $localisation): self
    {
        $this->localisation = $localisation;

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
     * @return Collection<int, Short>
     */
    public function getShorts(): Collection
    {
        return $this->shorts;
    }

    public function addShort(Short $short): self
    {
        if (!$this->shorts->contains($short)) {
            $this->shorts[] = $short;
            $short->setBoutique($this);
        }

        return $this;
    }

    public function removeShort(Short $short): self
    {
        if ($this->shorts->removeElement($short)) {
            // set the owning side to null (unless already changed)
            if ($short->getBoutique() === $this) {
                $short->setBoutique(null);
            }
        }

        return $this;
    }

    public function addShorts(Short $shorts): self
    {
        if (!$this->shorts->contains($shorts)) {
            $this->shorts->add($shorts);
            $shorts->setBoutique($this);
        }

        return $this;
    }

    public function removeShorts(Short $shorts): self
    {
        if ($this->shorts->removeElement($shorts)) {
            // set the owning side to null (unless already changed)
            if ($shorts->getBoutique() === $this) {
                $shorts->setBoutique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NotationBoutique>
     */
    public function getNotationBoutiques(): Collection
    {
        return $this->notationBoutiques;
    }

    public function addNotationBoutique(NotationBoutique $notationBoutique): self
    {
        if (!$this->notationBoutiques->contains($notationBoutique)) {
            $this->notationBoutiques->add($notationBoutique);
            $notationBoutique->setBoutique($this);
        }

        return $this;
    }

    public function removeNotationBoutique(NotationBoutique $notationBoutique): self
    {
        if ($this->notationBoutiques->removeElement($notationBoutique)) {
            // set the owning side to null (unless already changed)
            if ($notationBoutique->getBoutique() === $this) {
                $notationBoutique->setBoutique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AbonnementBoutique>
     */
    public function getAbonnementBoutiques(): Collection
    {
        return $this->abonnementBoutiques;
    }

    public function addAbonnementBoutique(AbonnementBoutique $abonnementBoutique): self
    {
        if (!$this->abonnementBoutiques->contains($abonnementBoutique)) {
            $this->abonnementBoutiques->add($abonnementBoutique);
            $abonnementBoutique->setBoutique($this);
        }

        return $this;
    }

    public function removeAbonnementBoutique(AbonnementBoutique $abonnementBoutique): self
    {
        if ($this->abonnementBoutiques->removeElement($abonnementBoutique)) {
            // set the owning side to null (unless already changed)
            if ($abonnementBoutique->getBoutique() === $this) {
                $abonnementBoutique->setBoutique(null);
            }
        }

        return $this;
    }
}
