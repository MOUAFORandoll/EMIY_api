<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]

class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "date", nullable: true)]
    private $dateCreated;

    #[ORM\ManyToOne(targetEntity: UserPlateform::class, inversedBy: "paniers")]
    private $user;

    #[ORM\Column(type: "string", length: 255)]
    private $codePanier;

    #[ORM\Column(type: "string", length: 255)]
    private $nomClient;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $prenomClient;

    #[ORM\Column(type: "string", length: 255)]
    private $phoneClient;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: "panier")]
    private $commandes;

    #[ORM\OneToMany(targetEntity: ListProduitPanier::class, mappedBy: "panier")]
    private $listProduitPaniers;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->commandes = new ArrayCollection();
        $this->listProduitPaniers = new ArrayCollection();
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

    public function getUser(): ?UserPlateform
    {
        return $this->user;
    }

    public function setUser(?UserPlateform $user): self
    {
        $this->user = $user;

        return $this;
    }


    public function getCodePanier(): ?string
    {
        return $this->codePanier;
    }

    public function setCodePanier(string $codePanier): self
    {
        $this->codePanier = $codePanier;

        return $this;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): self
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenomClient;
    }

    public function setPrenomClient(?string $prenomClient): self
    {
        $this->prenomClient = $prenomClient;

        return $this;
    }

    public function getPhoneClient(): ?string
    {
        return $this->phoneClient;
    }

    public function setPhoneClient(string $phoneClient): self
    {
        $this->phoneClient = $phoneClient;

        return $this;
    }

    // public function getListProduits(): ?array
    // {
    //     return $this->ListProduits;
    // }

    // public function setListProduits(array $ListProduits): self
    // {
    //     $this->ListProduits = $ListProduits;

    //     return $this;
    // }



    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setPanier($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getPanier() === $this) {
                $commande->setPanier(null);
            }
        }

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
            $listProduitPanier->setPanier($this);
        }

        return $this;
    }

    public function removeListProduitPanier(ListProduitPanier $listProduitPanier): self
    {
        if ($this->listProduitPaniers->removeElement($listProduitPanier)) {
            // set the owning side to null (unless already changed)
            if ($listProduitPanier->getPanier() === $this) {
                $listProduitPanier->setPanier(null);
            }
        }

        return $this;
    }

    public function getPrenomClient(): ?string
    {
        return $this->prenomClient;
    }
}
