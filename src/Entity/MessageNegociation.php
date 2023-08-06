<?php

namespace App\Entity;

use App\Repository\MessageNegociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageNegociationRepository::class)]
class MessageNegociation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'MessageNegociations')]
    private ?NegociationProduit $negociation = null;

    #[ORM\Column]
    private ?bool $emetteur = true;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\ManyToOne(inversedBy: 'messageNegociations')]
    private ?UserPlateform $initiateur = null;

    #[ORM\OneToMany(mappedBy: 'messageNegociation', targetEntity: Notification::class)]
    private Collection $notifications;
    public function __construct()
    {

        $this->dateEnvoi = new \DateTime();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNegociation(): ?NegociationProduit
    {
        return $this->negociation;
    }

    public function setNegociation(?NegociationProduit $negociation): self
    {
        $this->negociation = $negociation;

        return $this;
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

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setMessageNegociation($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getMessageNegociation() === $this) {
                $notification->setMessageNegociation(null);
            }
        }

        return $this;
    }
}
