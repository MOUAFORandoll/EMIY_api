<?php

namespace App\Entity;

use App\Repository\ShortCommentLikeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortCommentLikeRepository::class)]
class ShortCommentLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "boolean")]
    private $like_comment;

    #[ORM\Column(type: "date")]
    private $dateCreated;
    #[ORM\ManyToOne(inversedBy: 'shortCommentLikes')]
    private ?UserPlateform $client = null;

    #[ORM\ManyToOne(inversedBy: 'shortCommentLikes')]
    private ?ShortComment $shortComment = null;

    #[ORM\OneToMany(mappedBy: 'shortCommentLike', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->like_comment = true;
        $this->notifications = new ArrayCollection();
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

    public function isLike_comment(): ?bool
    {
        return $this->like_comment;
    }

    public function setLike_comment(bool $like_comment): self
    {
        $this->like_comment = $like_comment;

        return $this;
    }

    public function getClient(): ?UserPlateform
    {
        return $this->client;
    }

    public function setClient(?UserPlateform $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getShortComment(): ?ShortComment
    {
        return $this->shortComment;
    }

    public function setShortComment(?ShortComment $shortComment): static
    {
        $this->shortComment = $shortComment;

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
            $notification->setShortCommentLike($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getShortCommentLike() === $this) {
                $notification->setShortCommentLike(null);
            }
        }

        return $this;
    }
}
