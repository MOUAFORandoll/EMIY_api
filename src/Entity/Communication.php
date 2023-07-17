<?php

namespace App\Entity;

use App\Repository\CommunicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommunicationRepository::class)]
class Communication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'communications')]
    private ?UserPlateform $client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeCommunication = null;

    #[ORM\OneToMany(mappedBy: 'communication', targetEntity: MessageCommunication::class)]
    private Collection $messageCommunications;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();

        $this->messageCommunications = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCommunication(): ?string
    {
        return $this->codeCommunication;
    }

    public function setCodeCommunication(?string $codeCommunication): self
    {
        $this->codeCommunication = $codeCommunication;

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

    public function setDateCreated(?\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return Collection<int, MessageCommunication>
     */
    public function getMessageCommunications(): Collection
    {
        return $this->messageCommunications;
    }

    public function addMessageCommunication(MessageCommunication $messageCommunication): self
    {
        if (!$this->messageCommunications->contains($messageCommunication)) {
            $this->messageCommunications->add($messageCommunication);
            $messageCommunication->setCommunication($this);
        }

        return $this;
    }

    public function removeMessageCommunication(MessageCommunication $messageCommunication): self
    {
        if ($this->messageCommunications->removeElement($messageCommunication)) {
            // set the owning side to null (unless already changed)
            if ($messageCommunication->getCommunication() === $this) {
                $messageCommunication->setCommunication(null);
            }
        }

        return $this;
    }
}
