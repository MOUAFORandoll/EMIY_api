<?php

namespace App\Entity;

use App\Repository\MessageCommunicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageCommunicationRepository::class)]
class MessageCommunication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 

    #[ORM\Column]
    private ?bool $emetteur = true;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\ManyToOne(inversedBy: 'messageCommunications')]
    private ?UserPlateform $initiateur = null;

    #[ORM\ManyToOne(inversedBy: 'messageCommunications')]
    private ?Communication $communication = null;
    public function __construct()
    {

        $this->dateEnvoi = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
 
    public function isEmetteur(): ?bool
    {
        return $this->emetteur;
    }

    public function setEmetteur(bool $emetteur): self
    {
        $this->emetteur = $emetteur;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

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

    public function getCommunication(): ?Communication
    {
        return $this->communication;
    }

    public function setCommunication(?Communication $communication): self
    {
        $this->communication = $communication;

        return $this;
    }
    
}
