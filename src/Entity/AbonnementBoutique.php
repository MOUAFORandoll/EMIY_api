<?php

namespace App\Entity;

use App\Repository\AbonnementBoutiqueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: AbonnementBoutiqueRepository::class)]
class AbonnementBoutique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'abonnementBoutiques')]
    private ?Boutique $boutique = null;

    #[ORM\ManyToOne(inversedBy: 'abonnementBoutiques')]
    private ?UserPlateform $client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    /**
     * @ORM\Column(type="boolean")
     */
    #[ORM\Column(type: "boolean")]
    private $status = true;

    public function __construct()
    {

        $this->dateCreated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClient(): ?UserPlateform
    {
        return $this->client;
    }

    public function setClient(?UserPlateform $client): self
    {
        $this->client = $client;

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
    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
