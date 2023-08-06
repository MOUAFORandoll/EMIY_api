<?php

namespace App\Entity;

use App\Repository\UserReadShortRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserReadShortRepository::class)]
class UserReadShort
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userReadShorts')]
    private ?Short $short = null;

    #[ORM\ManyToOne(inversedBy: 'userReadShorts')]
    private ?UserPlateform $client = null;
    #[ORM\Column(type: "datetime", nullable: true)]
    private $dateCreated;

    public function __construct()
    {

        $this->dateCreated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShort(): ?Short
    {
        return $this->short;
    }

    public function setShort(?Short $short): static
    {
        $this->short = $short;

        return $this;
    }

    public function getClient(): ?UserPlateform
    {
        return $this->client;
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

    public function setClient(?UserPlateform $client): static
    {
        $this->client = $client;

        return $this;
    }
}
