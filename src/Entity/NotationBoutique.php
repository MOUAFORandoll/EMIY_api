<?php
// src/Entity/NotationBoutique.php

namespace App\Entity;

use App\Repository\NotationBoutiqueRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotationBoutiqueRepository::class)]
class NotationBoutique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "float")]
    private $note;

    #[ORM\Column(type: "date")]

    private $dateCreated;

    #[ORM\ManyToOne(inversedBy: 'notationProduits')]
    private ?UserPlateform $client = null;

    #[ORM\ManyToOne(inversedBy: 'notationBoutiques')]
    private ?Boutique $boutique = null;
 



    public function __construct()
    {
        $this->dateCreated = new \DateTime();
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

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

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

    public function getBoutique(): ?Boutique
    {
        return $this->boutique;
    }

    public function setBoutique(?Boutique $boutique): self
    {
        $this->boutique = $boutique;

        return $this;
    }
 
}
