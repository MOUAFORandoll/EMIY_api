<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PromotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]

class Promotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]

    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $libelle;

    #[ORM\Column(type: "date")]
    private $dateCreated;

    #[ORM\OneToMany(targetEntity: ListProduitPromotion::class, mappedBy: "promotion")]
    private $listProduitPromotions;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();


        $this->listProduitPromotions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, ListProduitPromotion>
     */
    public function getListProduitPromotions(): Collection
    {
        return $this->listProduitPromotions;
    }

    public function addListProduitPromotion(ListProduitPromotion $listProduitPromotion): self
    {
        if (!$this->listProduitPromotions->contains($listProduitPromotion)) {
            $this->listProduitPromotions[] = $listProduitPromotion;
            $listProduitPromotion->setPromotion($this);
        }

        return $this;
    }

    public function removeListProduitPromotion(ListProduitPromotion $listProduitPromotion): self
    {
        if ($this->listProduitPromotions->removeElement($listProduitPromotion)) {
            // set the owning side to null (unless already changed)
            if ($listProduitPromotion->getPromotion() === $this) {
                $listProduitPromotion->setPromotion(null);
            }
        }

        return $this;
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
}
