<?php

namespace App\Entity;

use App\Repository\ConnexionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: ConnexionRepository::class)]
class Connexion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime")]
    private $dateIn;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $dateOut;

    #[ORM\Column(type: "string", length: 255)]
    private $userAgent;

    #[ORM\ManyToOne(targetEntity: UserPlateform::class, inversedBy: "connexions")]
    private $user;

    #[ORM\Column(type: "string", length: 255)]
    private $ip; 

    public function __construct()
    {

        $this->dateIn = new \DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateIn(): ?\DateTimeInterface
    {
        return $this->dateIn;
    }

    public function setDateIn(\DateTimeInterface $dateIn): self
    {
        $this->dateIn = $dateIn;

        return $this;
    }

    public function getDateOut(): ?\DateTimeInterface
    {
        return $this->dateOut;
    }

    public function setDateOut(\DateTimeInterface $dateOut): self
    {
        $this->dateOut = $dateOut;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }



    public function getUer(): ?UserPlateform
    {
        return $this->user;
    }

    public function setUser(?UserPlateform $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUser(): ?UserPlateform
    {
        return $this->user;
    }
}
