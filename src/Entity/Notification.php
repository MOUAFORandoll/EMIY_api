<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?UserPlateform $initiateur = null;

    #[ORM\ManyToOne(inversedBy: 'notification_recepteur')]
    private ?UserPlateform $recepteur = null;

    #[ORM\Column(nullable: true)]
    private ?bool $read = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?TypeNotification $typeNotification = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?ShortComment $ShortCommentaire = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?MessageNegociation $messageNegociation = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?MessageCommunication $messageCommunication = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?ShortLike $shortLike = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?ShortCommentLike $shortCommentLike = null;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->read = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getInitiateur(): ?UserPlateform
    {
        return $this->initiateur;
    }

    public function setInitiateur(?UserPlateform $initiateur): static
    {
        $this->initiateur = $initiateur;

        return $this;
    }

    public function getRecepteur(): ?UserPlateform
    {
        return $this->recepteur;
    }

    public function setRecepteur(?UserPlateform $recepteur): static
    {
        $this->recepteur = $recepteur;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->read;
    }

    public function setRead(?bool $read): static
    {
        $this->read = $read;

        return $this;
    }

    public function getTypeNotification(): ?TypeNotification
    {
        return $this->typeNotification;
    }

    public function setTypeNotification(?TypeNotification $typeNotification): static
    {
        $this->typeNotification = $typeNotification;

        return $this;
    }

    public function getShortCommentaire(): ?ShortComment
    {
        return $this->ShortCommentaire;
    }

    public function setShortCommentaire(?ShortComment $ShortCommentaire): static
    {
        $this->ShortCommentaire = $ShortCommentaire;

        return $this;
    }

    public function getMessageNegociation(): ?MessageNegociation
    {
        return $this->messageNegociation;
    }

    public function setMessageNegociation(?MessageNegociation $messageNegociation): static
    {
        $this->messageNegociation = $messageNegociation;

        return $this;
    }

    public function getMessageCommunication(): ?MessageCommunication
    {
        return $this->messageCommunication;
    }

    public function setMessageCommunication(?MessageCommunication $messageCommunication): static
    {
        $this->messageCommunication = $messageCommunication;

        return $this;
    }

    public function getShortLike(): ?ShortLike
    {
        return $this->shortLike;
    }

    public function setShortLike(?ShortLike $shortLike): static
    {
        $this->shortLike = $shortLike;

        return $this;
    }

    public function getShortCommentLike(): ?ShortCommentLike
    {
        return $this->shortCommentLike;
    }

    public function setShortCommentLike(?ShortCommentLike $shortCommentLike): static
    {
        $this->shortCommentLike = $shortCommentLike;

        return $this;
    }
}
