<?php

namespace App\Entity;

use App\Repository\NegociationProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NegociationProduitRepository::class)]
class NegociationProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeNegociation = null;

    #[ORM\Column(length: 255,  nullable: true)]
    private ?string $prixNegocie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne(inversedBy: 'negociationProduits')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'negociationProduits')]
    private ?UserPlateform $initiateur = null;

    #[ORM\OneToMany(mappedBy: 'negociation', targetEntity: MessageNegociation::class)]
    private Collection $MessageNegociations;

    public function __construct()
    {

        $this->dateCreated = new \DateTime();

        $this->MessageNegociations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeNegociation(): ?string
    {
        return $this->codeNegociation;
    }

    public function setCodeNegociation(?string $codeNegociation): self
    {
        $this->codeNegociation = $codeNegociation;

        return $this;
    }

    public function getPrixNegocie(): ?string
    {
        return $this->prixNegocie;
    }

    public function setPrixNegocie(string $prixNegocie): self
    {
        $this->prixNegocie = $prixNegocie;

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

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getInitiateur(): ?UserPlateform
    {
        return $this->initiateur;
    }

    public function setInitiateur(?UserPlateform $initiateur): self
    {
        $this->initiateur = $initiateur;

        return $this;
    }

    /**
     * @return Collection<int, MessageNegociation>
     */
    public function getMessageNegociations(): Collection
    {
        return $this->MessageNegociations;
    }

    public function addMessageNegociation(MessageNegociation $MessageNegociation): self
    {
        if (!$this->MessageNegociations->contains($MessageNegociation)) {
            $this->MessageNegociations->add($MessageNegociation);
            $MessageNegociation->setNegociation($this);
        }

        return $this;
    }

    public function removeMessageNegociation(MessageNegociation $MessageNegociation): self
    {
        if ($this->MessageNegociations->removeElement($MessageNegociation)) {
            // set the owning side to null (unless already changed)
            if ($MessageNegociation->getNegociation() === $this) {
                $MessageNegociation->setNegociation(null);
            }
        }

        return $this;
    }
}
