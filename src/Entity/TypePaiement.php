<?php

namespace App\Entity;

use App\Repository\TypePaiementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypePaiementRepository::class)]

class TypePaiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    /**
     * Paiement livreur id : 0 et paiement boutique id : 1
     *
     */
    #[ORM\Column(type: "string", length: 255)]
    private $libelle;

    #[ORM\OneToMany(targetEntity: HistoriquePaiement::class, mappedBy: "typePaiement")]

    private $historiquePaiements;

    public function __construct()
    {
        $this->historiquePaiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $historiquePaiement->setTypePaiement($this);
        }

        return $this;
    }

    public function removeHistoriquePaiement(HistoriquePaiement $historiquePaiement): self
    {
        if ($this->historiquePaiements->removeElement($historiquePaiement)) {
            // set the owning side to null (unless already changed)
            if ($historiquePaiement->getTypePaiement() === $this) {
                $historiquePaiement->setTypePaiement(null);
            }
        }

        return $this;
    }
}
