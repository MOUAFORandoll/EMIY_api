<?php

namespace App\Entity;

use App\Repository\ShortLikeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortLikeRepository::class)]
class ShortLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "boolean")]
    private $like_short;

    #[ORM\Column(type: "date")]

    private $dateCreated;

    #[ORM\ManyToOne(inversedBy: 'shortLikes')]
    private ?Short $short = null;

    #[ORM\ManyToOne(inversedBy: 'shortLikes')]
    private ?UserPlateform $client = null;

    #[ORM\OneToMany(mappedBy: 'shortLike', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->like_short = true;
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

    public function isLike_short(): ?bool
    {
        return $this->like_short;
    }

    public function setLike_short(bool $like_short): self
    {
        $this->like_short = $like_short;

        return $this;
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

    public function setClient(?UserPlateform $client): static
    {
        $this->client = $client;

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
            $notification->setShortLike($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getShortLike() === $this) {
                $notification->setShortLike(null);
            }
        }

        return $this;
    }
}
