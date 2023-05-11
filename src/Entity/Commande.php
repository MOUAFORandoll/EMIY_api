<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $titre;

    #[ORM\Column(type: "string", length: 10000, nullable: true)]
    private $description;

    #[ORM\Column(type: "date")]
    private $dateCreated;

    #[ORM\Column(type: "string", length: 255)]
    private $codeCommande;

    #[ORM\Column(type: "string", length: 255)]
    private $codeClient;

    #[ORM\OneToMany(targetEntity: ListCommandeLivreur::class, mappedBy: "commande")]
    private $listCommandeLivreurs;

    #[ORM\ManyToOne(targetEntity: ModePaiement::class, inversedBy: "ModePaiement")]
    private $modePaiement;

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: "commandes")]
    private $panier;

    #[ORM\Column(type: "boolean")]
    private $statusBuy = false;

    #[ORM\Column(type: "integer")]

    // "0 => initialise et attente de sélection d'un livreur," .
    //     "1 => attente du livreur par les boutiques," .
    //     "2 => attente du livreur par le client et attente de validation codeClient," .
    //     "3 => commande livrée"

    private $statusFinish = 0;

    #[ORM\Column(type: "string", length: 1000000)]
    private $token;

    #[ORM\OneToMany(targetEntity: HistoriquePaiement::class, mappedBy: "commande")]
    private $historiquePaiements;

    #[ORM\ManyToOne(targetEntity: Localisation::class, inversedBy: "commandes")]
    private $localisation;
    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->listCommandeLivreurs = new ArrayCollection();
        $this->historiquePaiements = new ArrayCollection();
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

    public function getCodeCommande(): ?string
    {
        return $this->codeCommande;
    }

    public function setCodeCommande(string $codeCommande): self
    {
        $this->codeCommande = $codeCommande;

        return $this;
    }

    public function getCodeClient(): ?string
    {
        return $this->codeClient;
    }

    public function setCodeClient(string $codeClient): self
    {
        $this->codeClient = $codeClient;

        return $this;
    }


    /**
     * @return Collection<int, ListCommandeLivreur>
     */
    public function getListCommandeLivreurs(): Collection
    {
        return $this->listCommandeLivreurs;
    }

    public function addListCommandeLivreur(ListCommandeLivreur $listCommandeLivreur): self
    {
        if (!$this->listCommandeLivreurs->contains($listCommandeLivreur)) {
            $this->listCommandeLivreurs[] = $listCommandeLivreur;
            $listCommandeLivreur->setCommande($this);
        }

        return $this;
    }

    public function removeListCommandeLivreur(ListCommandeLivreur $listCommandeLivreur): self
    {
        if ($this->listCommandeLivreurs->removeElement($listCommandeLivreur)) {
            // set the owning side to null (unless already changed)
            if ($listCommandeLivreur->getCommande() === $this) {
                $listCommandeLivreur->setCommande(null);
            }
        }

        return $this;
    }


    public function getModePaiement(): ?ModePaiement
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?ModePaiement $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
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

    public function isStatusBuy(): ?bool
    {
        return $this->statusBuy;
    }

    public function setStatusBuy(bool $statusBuy): self
    {
        $this->statusBuy = $statusBuy;

        return $this;
    }

    public function getStatusFinish(): ?int
    {
        return $this->statusFinish;
    }

    public function setStatusFinish(int $statusFinish): self
    {
        $this->statusFinish = $statusFinish;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, HistoriquePaiement>
     */
    public function getHistoriquePaiements(): Collection
    {
        return $this->historiquePaiements;
    }

    public function addHistoriquePaiement(HistoriquePaiement $historiquePaiement): self
    {
        if (!$this->historiquePaiements->contains($historiquePaiement)) {
            $this->historiquePaiements[] = $historiquePaiement;
            $historiquePaiement->setCommande($this);
        }

        return $this;
    }

    public function removeHistoriquePaiement(HistoriquePaiement $historiquePaiement): self
    {
        if ($this->historiquePaiements->removeElement($historiquePaiement)) {
            // set the owning side to null (unless already changed)
            if ($historiquePaiement->getCommande() === $this) {
                $historiquePaiement->setCommande(null);
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
}
