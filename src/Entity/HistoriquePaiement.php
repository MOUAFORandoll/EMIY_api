<?php

namespace App\Entity;

use App\Repository\HistoriquePaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriquePaiementRepository::class)]
class HistoriquePaiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $montant;

    #[ORM\Column(type: "datetime")]
    private $dateCreated;

    #[ORM\Column(type: "string", length: 255)]
    private $token;

    #[ORM\ManyToOne(targetEntity: UserPlateform::class, inversedBy: "historiquePaiements")]
    private $user;

    #[ORM\ManyToOne(targetEntity: TypePaiement::class, inversedBy: "historiquePaiements")]
    private $typePaiement;

    #[ORM\Column(type: "boolean")]
    private $status;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "historiquePaiements")]
    private $commande;
    public function __construct()
    {
        $this->dateCreated = new \DateTime();

        $this->status = false;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

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
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTypePaiement(): ?TypePaiement
    {
        return $this->typePaiement;
    }

    public function setTypePaiement(?TypePaiement $typePaiement): self
    {
        $this->typePaiement = $typePaiement;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}
