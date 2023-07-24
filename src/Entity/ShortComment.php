<?php

namespace App\Entity;

use App\Repository\ShortCommentRepository;
use App\Traits\SoftDeletableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortCommentRepository::class)]
class ShortComment
{
    use SoftDeletableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $dateCreated;
    #[ORM\ManyToOne(inversedBy: 'shortComments')]
    private ?Short $short = null;

    #[ORM\ManyToOne(inversedBy: 'shortComments')]
    private ?UserPlateform $client = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\OneToMany(mappedBy: 'shortCommentm', targetEntity: ShortCommentLike::class)]
    private Collection $shortCommentLikes;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'shortComments')]
    private ?self $reference_commentaire = null;

    #[ORM\OneToMany(mappedBy: 'reference_commentaire', targetEntity: self::class)]
    private Collection $shortComments;


    public function __construct()
    {

        $this->dateCreated = new \DateTime();

        $this->shortCommentLikes = new ArrayCollection();
        $this->shortComments = new ArrayCollection();
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

    public function setClient(?UserPlateform $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, ShortCommentLike>
     */
    public function getShortCommentLikes(): Collection
    {
        return $this->shortCommentLikes;
    }

    public function addShortCommentLike(ShortCommentLike $shortCommentLike): static
    {
        if (!$this->shortCommentLikes->contains($shortCommentLike)) {
            $this->shortCommentLikes->add($shortCommentLike);
            $shortCommentLike->setShortComment($this);
        }

        return $this;
    }

    public function removeShortCommentLike(ShortCommentLike $shortCommentLike): static
    {
        if ($this->shortCommentLikes->removeElement($shortCommentLike)) {
            // set the owning side to null (unless already changed)
            if ($shortCommentLike->getShortComment() === $this) {
                $shortCommentLike->setShortComment(null);
            }
        }

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

    public function getReferenceCommentaire(): ?self
    {
        return $this->reference_commentaire;
    }

    public function setReferenceCommentaire(?self $reference_commentaire): static
    {
        $this->reference_commentaire = $reference_commentaire;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getShortComments(): Collection
    {
        return $this->shortComments;
    }

    public function addShortComment(self $shortComment): static
    {
        if (!$this->shortComments->contains($shortComment)) {
            $this->shortComments->add($shortComment);
            $shortComment->setReferenceCommentaire($this);
        }

        return $this;
    }

    public function removeShortComment(self $shortComment): static
    {
        if ($this->shortComments->removeElement($shortComment)) {
            // set the owning side to null (unless already changed)
            if ($shortComment->getReferenceCommentaire() === $this) {
                $shortComment->setReferenceCommentaire(null);
            }
        }

        return $this;
    }
}
