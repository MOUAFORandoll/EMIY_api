<?php

namespace App\Entity;

use App\Repository\ShortRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortRepository::class)]

class Short
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $src;

    #[ORM\Column(type: "string", length: 255)]
    private $titre;

    #[ORM\Column(type: "boolean")]
    private $status = true;

    #[ORM\Column(type: "string", length: 10000, nullable: true)]
    private $description;

    #[ORM\Column(type: "date")]
    private $dateCreated;

    #[ORM\ManyToOne(targetEntity: Boutique::class, inversedBy: "shorts")]
    private $boutique;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $preview = null;

    #[ORM\OneToMany(mappedBy: 'short', targetEntity: ShortLike::class)]
    private Collection $shortLikes;

    #[ORM\OneToMany(mappedBy: 'short', targetEntity: ShortComment::class)]
    private Collection $shortComments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeShort = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function setSrc(string $src): self
    {
        $this->src = $src;

        return $this;
    }
    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->shortLikes = new ArrayCollection();
        $this->shortComments = new ArrayCollection();
    }
    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

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
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPreview(): ?string
    {
        return $this->preview;
    }

    public function setPreview(string $preview): self
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * @return Collection<int, ShortLike>
     */
    public function getShortLikes(): Collection
    {
        return $this->shortLikes;
    }

    public function addShortLike(ShortLike $shortLike): static
    {
        if (!$this->shortLikes->contains($shortLike)) {
            $this->shortLikes->add($shortLike);
            $shortLike->setShort($this);
        }

        return $this;
    }

    public function removeShortLike(ShortLike $shortLike): static
    {
        if ($this->shortLikes->removeElement($shortLike)) {
            // set the owning side to null (unless already changed)
            if ($shortLike->getShort() === $this) {
                $shortLike->setShort(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShortComment>
     */
    public function getShortComments(): Collection
    {
        return $this->shortComments;
    }

    public function addShortComment(ShortComment $shortComment): static
    {
        if (!$this->shortComments->contains($shortComment)) {
            $this->shortComments->add($shortComment);
            $shortComment->setShort($this);
        }

        return $this;
    }

    public function removeShortComment(ShortComment $shortComment): static
    {
        if ($this->shortComments->removeElement($shortComment)) {
            // set the owning side to null (unless already changed)
            if ($shortComment->getShort() === $this) {
                $shortComment->setShort(null);
            }
        }

        return $this;
    }

    public function getCodeShort(): ?string
    {
        return $this->codeShort;
    }

    public function setCodeShort(?string $codeShort): static
    {
        $this->codeShort = $codeShort;

        return $this;
    }
}
